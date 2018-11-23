<?php
namespace RindowTest\I18n\GettextTest;

use PHPUnit\Framework\TestCase;
// Test Target Classes
use Rindow\Stdlib\I18n\Gettext;

class GettextTest extends TestCase
{
    static $RINDOW_TEST_RESOURCES;

    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../../resources';
    	//Rindow\Stdlib\I18n\Gettext::initialize();
    }

    public static function tearDownAfterClass()
    {
    }

    public function testHeader()
    {
        $fd = fopen(self::$RINDOW_TEST_RESOURCES.'/php/messages/en_US/LC_MESSAGES/domain1.mo','rb');
        $gettext = new Gettext();
        $header = $gettext->readHeader($fd);
        $result = $gettext->buildTextDomain($fd,$header);
        fclose($fd);
        $this->assertEquals(2,count($result['text']));
        $this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',
            $result['text']['{rindow.test.gettext.domain1.messages}']);
        $this->assertEquals('Welcome to My PHP Application',
            $result['text']['{rindow.test.phptest.gettext.messages}']);
        $this->assertTrue(isset($result['header']));
    }

    public function testGettext()
    {
        $gettext = Gettext::factory();
        $this->assertEquals('abc',$gettext->getText('abc'));

        $gettext->bindTextDomain('domain1',self::$RINDOW_TEST_RESOURCES.'/php/messages');
        $this->assertEquals('gettext translation test textdomain:domain1 locale:en_US',
            $gettext->getText('{rindow.test.gettext.domain1.messages}','domain1','en_US'));
        $this->assertEquals('Welcome to My PHP Application',
            $gettext->getText('{rindow.test.phptest.gettext.messages}','domain1','en_US'));
    }
}