<?php
namespace RindowTest\I18n\TranslatorTest;

use PHPUnit\Framework\TestCase;
use Rindow\Container\ModuleManager;

// Test Target Classes
use Rindow\Stdlib\I18n\Translator;

class TranslatorTest extends TestCase
{
    static $RINDOW_TEST_RESOURCES;

    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../../resources';
    	//Rindow\Stdlib\I18n\Translator::initialize();
    }

    public static function tearDownAfterClass()
    {
    }

    public function setUp()
    {
        usleep( RINDOW_TEST_CLEAR_CACHE_INTERVAL );
        \Rindow\Stdlib\Cache\CacheFactory::clearCache();
        usleep( RINDOW_TEST_CLEAR_CACHE_INTERVAL );
    }

    public function testNormal()
    {
    	$translator = new Translator();
    	$translator->bindTextDomain('domain1' ,self::$RINDOW_TEST_RESOURCES.'/php/messages');
        $translator->setLocale('en_US');
        $translator->setTextDomain('domain1');
    	$result = $translator->translate("{rindow.test.gettext.domain1.messages}");
		$this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',$result);

    	$result = $translator->translate("{rindow.test.gettext.domain1.messages}",null,'ja_JP');
		$this->assertEquals('gettext translation test textdomain:domain1 locale:ja_JP',$result);

    	$result = $translator->translate("{rindow.test.gettext.domain1.messages}");
		$this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',$result);

    	$result = $translator->translate("{rindow.test.gettext.domain1.messages}",'hogehoge');
		$this->assertEquals('{rindow.test.gettext.domain1.messages}',$result);
    }

    public function testMultiSession()
    {
    	$translator1 = new Translator();
    	$translator1->bindTextDomain('domain1' ,self::$RINDOW_TEST_RESOURCES.'/php/messages','en_US');
        $translator1->setLocale('en_US');
        $translator1->setTextDomain('domain1');

    	$translator2 = new Translator();
    	$translator2->bindTextDomain('domain2' ,self::$RINDOW_TEST_RESOURCES.'/php/messages','en_US');
        $translator2->setLocale('en_US');
        $translator2->setTextDomain('domain2');

    	$result = $translator1->translate("{rindow.test.gettext.domain1.messages}");
		$this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',$result);

    	$result = $translator2->translate("{rindow.test.gettext.domain1.messages}");
		$this->assertEquals('gettext translation test textdomain:domain2 locale:en_US',$result);

    	$result = $translator2->translate("{rindow.test.gettext.domain1.messages}",'domain1');
		$this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',$result);

    	$result = $translator2->translate("{rindow.test.gettext.domain1.messages}");
		$this->assertEquals('gettext translation test textdomain:domain2 locale:en_US',$result);

    	$result = $translator1->translate("{rindow.test.gettext.domain1.messages}");
		$this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',$result);
    }

    public function testOnModule()
    {
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Stdlib\I18n\Module' => true,
                ),
            ),
            'translator' => array(
                'translation_file_patterns' => array(
                    array(
                        'type'        => 'Gettext',
                        'base_dir'    => self::$RINDOW_TEST_RESOURCES.'/php/messages',
                        'text_domain' => 'domain3',
                    ),
                ),
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $translator = $sm->get('Rindow\Stdlib\I18n\DefaultTranslator');

        $result = $translator->translate("{rindow.test.gettext.domain1.messages}",'domain3','en_US');
        $this->assertEquals('gettext translation test textdomain:domain3 locale:en_US',$result);
    }
}