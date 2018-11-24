<?php
namespace RindowTest\StdCache\CacheWithApcuTest;

use PHPUnit\Framework\TestCase;
use ArrayObject;

// Test Target Classes
use Rindow\Stdlib\Cache\CacheFactory;
use Rindow\Stdlib\Cache\CacheChain;
use Rindow\Stdlib\Cache\FileCache;
use Rindow\Stdlib\Cache\ApcCache;
use Rindow\Stdlib\Cache\ArrayCache;

class Test extends TestCase
{
    protected static $backupEnableMemCache;
    protected static $backupEnableFileCache;
    protected static $backupForceFileCache;
    protected static $backupFileCachePath;

    public static function setUpBeforeClass()
    {
        CacheFactory::clearFileCache(RINDOW_TEST_CACHE);
        //$loader = Rindow\Loader\Autoloader::factory();
        //$loader->setNameSpace('AcmeTest',RINDOW_TEST_CACHE.'/AcmeTest');
        self::$backupEnableMemCache  = CacheFactory::$enableMemCache;
        self::$backupEnableFileCache = CacheFactory::$enableFileCache;
        self::$backupForceFileCache  = CacheFactory::$forceFileCache;
        self::$backupFileCachePath   = CacheFactory::$fileCachePath;
    }

    public static function tearDownAfterClass()
    {
        CacheFactory::clearFileCache(RINDOW_TEST_CACHE);
        CacheFactory::$enableMemCache  = self::$backupEnableMemCache ;
        CacheFactory::$enableFileCache = self::$backupEnableFileCache;
        CacheFactory::$forceFileCache  = self::$backupForceFileCache ;
        CacheFactory::$fileCachePath   = self::$backupFileCachePath  ;
    }

    public function setUp()
    {
    }

    public function testCacheChain()
    {
        $storage = new ArrayObject();
        $mem = new ArrayObject();
        $cache = new CacheChain($storage,$mem);

        $cache['a'] = 'A';
        $cache['b'] = 'B';

        $this->assertEquals('A',$cache['a']);
        $this->assertEquals('B',$cache['b']);

        $cache2 = new CacheChain($cache);
        $this->assertEquals('A',$cache2['a']);
        $this->assertEquals('B',$cache2['b']);

        unset($cache['a']);
        $this->assertFalse($mem->offsetExists('a'));
        $this->assertFalse(isset($cache['a']));
        $this->assertFalse($cache->containsKey('a'));
        unset($cache['b']);
        $this->assertFalse($mem->offsetExists('b'));
        $this->assertFalse($cache->containsKey('b'));

        $this->assertTrue($mem->offsetExists('a'));
        list($status,$value) = $mem->offsetGet('a');
        $this->assertFalse($status);
        $this->assertTrue($mem->offsetExists('b'));
        list($status,$value) = $mem->offsetGet('b');
        $this->assertFalse($status);
        $this->assertFalse($storage->offsetExists('a'));
        $this->assertFalse($storage->offsetExists('b'));

        $cache3 = new CacheChain();
        $cache3['a'] = 'A';
        $cache3['b'] = 'B';
        $this->assertEquals('A',$cache3['a']);
        $this->assertEquals('B',$cache3['b']);
        unset($cache3['a']);
        $this->assertFalse(isset($cache3['a']));
        $this->assertFalse($cache3->containsKey('a'));
        unset($cache3['b']);
        $this->assertFalse($cache3->containsKey('b'));
        $cache3->put('c','C');
        $this->assertEquals('C',$cache3->get('c'));
        $this->assertEquals(null,$cache3->get('d'));
        $this->assertEquals(null,$cache3->get('d'));
        $this->assertEquals('E',$cache3->get('e',null,function($cache,$offset,&$value) {
            $value = 'E';
            return true;
        }));
        $this->assertEquals('E',$cache3->get('e'));
        $this->assertFalse(isset($cache3['f']));
        $this->assertEquals('F',$cache3->get('f',null,function($cache,$offset,&$value) {
            $value = 'F';
            return true;
        }));


        $cache4 = new CacheChain(new ArrayCache());
        $cache4->put('c','C');
        $this->assertEquals('C',$cache4->get('c'));
        $this->assertEquals(null,$cache4->get('d'));
        $this->assertEquals(null,$cache4->get('d'));
        $this->assertEquals('E',$cache4->get('e',null,function($cache,$offset,&$value) {
            $value = 'E';
            return true;
        }));
        $this->assertEquals('E',$cache4->get('e'));
        $this->assertFalse(isset($cache4['f']));
        $this->assertEquals('F',$cache4->get('f',null,function($cache,$offset,&$value) {
            $value = 'F';
            return true;
        }));

        $this->assertEquals(array(),$cache4->get('g',null,function($cache,$offset,&$value) {
            $value = array();
            return true;
        }));
    }

    /**
     * @requires PHPUnit 6.0.0
     * @expectedException        PHPUnit\Framework\Error\Notice
     * @expectedExceptionMessage Offset invalid or out of range
     */
    public function testOutOfRangeException1()
    {
        $storage = new ArrayObject();
        $cache = new CacheChain($storage);
        $a = $cache['a'];
    }

    public function testOutOfRangeException2()
    {
        $storage = new ArrayObject();
        $cache = new CacheChain($storage);
        try {
            $a = $cache['a'];
        } catch(\PHPUnit\Framework\Error\Notice $e) {
            $this->assertStringStartsWith('Offset invalid or out of range',$e->getMessage());
            return;
        } catch(\PHPUnit_Framework_Error_Notice $e) {
            $this->assertStringStartsWith('Offset invalid or out of range',$e->getMessage());
            return;
        }
        $this->assertFalse(true,'uncatch out of range');
    }

    public function testUnsetOutOfRange()
    {
        $storage = new ArrayObject();
        $cache = new CacheChain($storage);
        unset($cache['a']);
        $this->assertTrue(true);
    }

    public function testPushToArray()
    {
        $storage = new ArrayObject();
        $cache = new CacheChain($storage);
        $cache[] = 'a';
        $this->assertEquals('a',$cache[0]);
        $this->assertEquals('a',$storage[0]);
    }

    public function testSquare()
    {
        $storage = new ArrayObject();
        $cache = new CacheChain($storage);
        if(@isset($cache['a']['b']))
            $this->assertTrue(false);
        else
            $this->assertTrue(true);
        $cache['a'] = new ArrayObject(); // array() makes error.
        $cache['a']['b'] = 'a';
        $this->assertEquals('a',$cache['a']['b']);
    }

    public function testFileStore()
    {
        $cache = new FileCache(RINDOW_TEST_CACHE.'/cache');

        $cache['a'] = 'A';
        $cache['b\\b'] = 'B';

        $this->assertEquals('A',$cache['a']);
        $this->assertEquals('B',$cache['b\\b']);

        unset($cache['a']);
        $this->assertFalse(isset($cache['a']));
        $this->assertFalse($cache->containsKey('a'));
        unset($cache['b\\b']);

        $cache2 = new CacheChain($cache);
        $cache2['a'] = 'A';
        $cache2['b\\b'] = 'B';

        $this->assertEquals('A',$cache2['a']);
        $this->assertEquals('B',$cache2['b\\b']);

        unset($cache2['a']);
        $this->assertFalse(isset($cache2['a']));
        $this->assertFalse($cache2->containsKey('a'));
        unset($cache2['b\\b']);

    }

    /**
     * @requires extension apcu
     */
    public function testApcStore()
    {
        if(!ini_get('apc.enable_cli')) {
            $this->markTestSkipped();
            return;
        }

        $cache = new ApcCache(RINDOW_TEST_CACHE.'/cache');

        $cache['a'] = 'A';
        $cache['b\\b'] = 'B';

        $this->assertEquals('A',$cache['a']);
        $this->assertEquals('B',$cache['b\\b']);

        unset($cache['a']);
        $this->assertFalse(isset($cache['a']));
        $this->assertFalse($cache->containsKey('a'));
        unset($cache['b\\b']);

        $cache2t = new ApcCache(RINDOW_TEST_CACHE.'/cache2',1);

        $cache2t['a'] = 'A';
        $this->assertTrue($cache2t->containsKey('a'));
        // not work
        //sleep(10);
        //$this->assertFalse($cache2t->containsKey('a'));

        $cache2 = new CacheChain($cache);
        $cache2['a'] = 'A';
        $cache2['b\\b'] = 'B';

        $this->assertEquals('A',$cache2['a']);
        $this->assertEquals('B',$cache2['b\\b']);

        unset($cache2['a']);
        $this->assertFalse(isset($cache2['a']));
        $this->assertFalse($cache2->containsKey('a'));
        unset($cache2['b\\b']);
    }

    /**
     * @requires extension apcu
     */
    public function testSecondaryCache()
    {
        $path = RINDOW_TEST_CACHE.'/cache/chain';
        $apc = new ApcCache($path);
        $file = new FileCache($path);
        $secondary = new CacheChain($file,$apc);

        $secondary['a'] = 'A';
        $secondary['b\\b'] = 'B';

        $this->assertEquals('A',$secondary['a']);
        $this->assertEquals('B',$secondary['b\\b']);

        unset($secondary['a']);
        $this->assertFalse(isset($secondary['a']));
        $this->assertFalse($secondary->containsKey('a'));
        unset($secondary['b\\b']);

        $primary = new CacheChain($secondary);

        $primary['a'] = 'A';
        $primary['b\\b'] = 'B';

        $this->assertEquals('A',$primary['a']);
        $this->assertEquals('B',$primary['b\\b']);

        unset($primary['a']);
        $this->assertFalse(isset($primary['a']));
        $this->assertFalse($primary->containsKey('a'));
        unset($primary['b\\b']);

        $primary['a'] = 'A';
        $primary['b\\b'] = 'B';

        // On Other Instance 
        $path = RINDOW_TEST_CACHE.'/cache/chain';
        $apc = new ApcCache($path);
        $file = new FileCache($path);
        $secondary = new CacheChain($file,$apc);
        $primary = new CacheChain($secondary);

        $this->assertEquals('A',$primary['a']);
        $this->assertEquals('B',$primary['b\\b']);
    }

    public function testFactory()
    {
        CacheFactory::$enableMemCache = false;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = false;
        $cache = CacheFactory::newInstance('aa');
        $this->assertEquals('Rindow\Stdlib\Cache\FileCache',get_class($cache->getStorage()));
        $this->assertEquals('Rindow\Stdlib\Cache\ArrayCache',get_class($cache->getCache()));

        CacheFactory::$enableMemCache = false;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = false;
        $cache = CacheFactory::newInstance('aa');
        $this->assertEquals('Rindow\Stdlib\Cache\ArrayCache',get_class($cache->getCache()));
        $this->assertEquals('Rindow\Stdlib\Cache\FileCache',get_class($cache->getStorage()));

        CacheFactory::$enableMemCache = false;
        CacheFactory::$enableFileCache = false;
        CacheFactory::$forceFileCache = false;
        $cache = CacheFactory::newInstance('aa');
        $this->assertEquals('Rindow\Stdlib\Cache\ArrayCache',get_class($cache));

        CacheFactory::$enableMemCache = true;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = false;
    }

    /**
     * @requires extension apcu
     */
    public function testFactoryWithApc()
    {
        CacheFactory::$enableMemCache = true;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = false;
        $cache = CacheFactory::newInstance('aa');
        $this->assertEquals('Rindow\Stdlib\Cache\ApcCache',get_class($cache->getStorage()));
        $this->assertEquals('Rindow\Stdlib\Cache\ArrayCache',get_class($cache->getCache()));
        
        CacheFactory::$enableMemCache = true;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = true;
        $cache = CacheFactory::newInstance('aa');
        $this->assertEquals('Rindow\Stdlib\Cache\CacheChain',get_class($cache->getStorage()));
        $this->assertEquals('Rindow\Stdlib\Cache\ArrayCache',get_class($cache->getCache()));
        $storage = $cache->getStorage();
        $this->assertEquals('Rindow\Stdlib\Cache\FileCache',get_class($storage->getStorage()));
        $this->assertEquals('Rindow\Stdlib\Cache\ApcCache',get_class($storage->getCache()));

        CacheFactory::$enableMemCache = true;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = false;
    }

    public function testFactoryCachePath()
    {
        $tmpdir = str_replace('\\', '/', sys_get_temp_dir());
        $cachedir = RINDOW_TEST_CACHE.'/cache';
        CacheFactory::$enableMemCache = false;
        CacheFactory::$enableFileCache = true;
        CacheFactory::$forceFileCache = true;
        CacheFactory::$fileCachePath = null;

        $cache = CacheFactory::newInstance('Path');
        $fileCache = $cache->getStorage();
        $this->assertEquals('Rindow\Stdlib\Cache\FileCache',get_class($fileCache));
        $this->assertEquals($tmpdir.'/Path',$fileCache->getCachePath());

        $cache = CacheFactory::newInstance('/path');
        $cache['item\test'] = 'a';

        CacheFactory::$fileCachePath = $cachedir;
        $cache = CacheFactory::newInstance('Path');
        $fileCache = $cache->getStorage();
        $this->assertEquals('Rindow\Stdlib\Cache\FileCache',get_class($fileCache));
        $this->assertEquals(str_replace('\\', '/',$cachedir).'/Path',$fileCache->getCachePath());

        $cache = CacheFactory::newInstance('Class\Name\Space\Class');
        $fileCache = $cache->getStorage();
        $this->assertEquals('Rindow\Stdlib\Cache\FileCache',get_class($fileCache));
        $this->assertEquals(str_replace('\\', '/',$cachedir).'/Class/Name/Space/Class',$fileCache->getCachePath());
    }

    public function testSetConfig()
    {
        CacheFactory::$enableMemCache = false;
        CacheFactory::$enableFileCache = false;
        CacheFactory::$forceFileCache = false;
        CacheFactory::$fileCachePath = null;

        $config = array(
            'enableMemCache'  => true,
            'enableFileCache' => true,
            'forceFileCache'  => true,
            'fileCachePath'   => '/abc/def',
        );
        CacheFactory::setConfig($config);

        $this->assertEquals(true, CacheFactory::$enableMemCache);
        $this->assertEquals(true, CacheFactory::$enableFileCache);
        $this->assertEquals(true, CacheFactory::$forceFileCache);
        $this->assertEquals('/abc/def', CacheFactory::$fileCachePath);

        $config = array(
            'enableMemCache'  => false,
            'enableFileCache' => false,
            'forceFileCache'  => false,
            'fileCachePath'   => null,
        );
        CacheFactory::setConfig($config);

        $this->assertEquals(false, CacheFactory::$enableMemCache);
        $this->assertEquals(false, CacheFactory::$enableFileCache);
        $this->assertEquals(false, CacheFactory::$forceFileCache);
        $this->assertEquals(null, CacheFactory::$fileCachePath);

        $config = array(
            'enableMemCache'  => true,
            'enableFileCache' => true,
            'forceFileCache'  => true,
            'fileCachePath'   => '/abc/def',
        );
        CacheFactory::setConfig($config);

        $this->assertEquals(true, CacheFactory::$enableMemCache);
        $this->assertEquals(true, CacheFactory::$enableFileCache);
        $this->assertEquals(true, CacheFactory::$forceFileCache);
        $this->assertEquals('/abc/def', CacheFactory::$fileCachePath);

        CacheFactory::setConfig('a');

        $this->assertEquals(true, CacheFactory::$enableMemCache);
        $this->assertEquals(true, CacheFactory::$enableFileCache);
        $this->assertEquals(true, CacheFactory::$forceFileCache);
        $this->assertEquals('/abc/def', CacheFactory::$fileCachePath);

        CacheFactory::setConfig();

        $this->assertEquals(true, CacheFactory::$enableMemCache);
        $this->assertEquals(true, CacheFactory::$enableFileCache);
        $this->assertEquals(true, CacheFactory::$forceFileCache);
        $this->assertEquals('/abc/def', CacheFactory::$fileCachePath);
    }

    public function testSpecialCharactorInOffset()
    {
        $fileCache = new FileCache();
        $this->assertEquals('//%3A%2A%3F%22%3C%3E%7C%46',
            $fileCache->transFromOffsetToPath('/\\:*?"<>|.'));
    }

    /**
     * @requires extension apcu
     */
    public function testApcGetAndPutAndRemoveStyle()
    {
        if(!ini_get('apc.enable_cli')) {
            $this->markTestSkipped();
            return;
        }

        $cache = new ApcCache(RINDOW_TEST_CACHE.'/cache');

        $cache->put('a', 'A');
        $cache->put('b\\b', 'B');

        $this->assertEquals('A',$cache->get('a'));
        $this->assertEquals('B',$cache->get('b\\b'));

        $this->assertTrue($cache->containsKey('a'));

        $cache->put('a','AAA');
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->put('a','XXX',$addMode=true);
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->remove('a');

        $this->assertFalse($cache->containsKey('a'));
        $cache->remove('b\\b');
    }

    public function testFileGetAndPutAndRemoveStyle()
    {
        $cache = new FileCache(RINDOW_TEST_CACHE.'/cache');

        $cache->put('a', 'A');
        $cache->put('b\\b', 'B');

        $this->assertEquals('A',$cache->get('a'));
        $this->assertEquals('B',$cache->get('b\\b'));

        $this->assertTrue($cache->containsKey('a'));

        $cache->put('a','AAA');
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->put('a','XXX',$addMode=true);
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->remove('a');

        $this->assertFalse($cache->containsKey('a'));
        $cache->remove('b\\b');
    }

    public function testArrayGetAndPutAndRemoveStyle()
    {
        $cache = new ArrayCache();

        $cache->put('a', 'A');
        $cache->put('b\\b', 'B');

        $this->assertEquals('A',$cache->get('a'));
        $this->assertEquals('B',$cache->get('b\\b'));

        $this->assertTrue($cache->containsKey('a'));

        $cache->put('a','AAA');
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->put('a','XXX',$addMode=true);
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->remove('a');

        $this->assertFalse($cache->containsKey('a'));
        $cache->remove('b\\b');
    }

    public function testCacheChainGetAndPutAndRemoveStyle()
    {
        $cache = new CacheChain(new ArrayCache(),new ArrayCache());

        $cache->put('a', 'A');
        $cache->put('b\\b', 'B');

        $this->assertEquals('A',$cache->get('a'));
        $this->assertEquals('B',$cache->get('b\\b'));

        $this->assertTrue($cache->containsKey('a'));

        $cache->put('a','AAA');
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->put('a','XXX',$addMode=true);
        $this->assertEquals('AAA',$cache->get('a'));

        $cache->remove('a');

        $this->assertFalse($cache->containsKey('a'));
        $cache->remove('b\\b');
    }

    /**
     * @requires extension apcu
     */
    public function testApcGetWithCallback()
    {
        if(!ini_get('apc.enable_cli')) {
            $this->markTestSkipped();
            return;
        }

        $phpunit = $this;
        $cache = new ApcCache(RINDOW_TEST_CACHE.'/cache');
        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey',$key);
            $value = 12345;
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$callback));
        $this->assertEquals(12345,$cache->get('testkey'));
        $donotcallback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertTrue(false);
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$donotcallback));

        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey2',$key);
            return false;
        };
        $this->assertEquals('default',$cache->get('testkey2','default',$callback));

        $cache = new ApcCache(RINDOW_TEST_CACHE.'/cache');
        $this->assertEquals(null,$cache->get('testkey2'));
    }

    public function testFileGetWithCallback()
    {
        $phpunit = $this;
        $cache = new FileCache(RINDOW_TEST_CACHE.'/cache');
        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey',$key);
            $value = 12345;
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$callback));
        $this->assertEquals(12345,$cache->get('testkey'));
        $donotcallback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertTrue(false);
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$donotcallback));

        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey2',$key);
            return false;
        };
        $this->assertEquals('default',$cache->get('testkey2','default',$callback));

        $cache = new FileCache(RINDOW_TEST_CACHE.'/cache');
        $this->assertEquals(null,$cache->get('testkey2'));
    }

    public function testArrayGetWithCallback()
    {
        $phpunit = $this;
        $cache = new ArrayCache();
        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey',$key);
            $value = 12345;
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$callback));
        $this->assertEquals(12345,$cache->get('testkey'));
        $donotcallback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertTrue(false);
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$donotcallback));

        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey2',$key);
            return false;
        };
        $this->assertEquals('default',$cache->get('testkey2','default',$callback));

        $cache = new ArrayCache();
        $this->assertEquals(null,$cache->get('testkey2'));
    }


    public function testCacheChainGetWithCallback()
    {
        $store = new ArrayCache();
        $mem   = new ArrayCache();
        $phpunit = $this;
        $cache = new CacheChain($store,$mem);
        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey',$key);
            $value = 12345;
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$callback));
        $this->assertEquals(12345,$cache->get('testkey'));
        $donotcallback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertTrue(false);
            return true;
        };
        $this->assertEquals(12345,$cache->get('testkey','default',$donotcallback));

        $callback = function ($cache,$key,&$value) use ($phpunit) {
            $phpunit->assertEquals('testkey2',$key);
            return false;
        };
        $this->assertEquals('default',$cache->get('testkey2','default',$callback));

        $cache = new CacheChain($store,$mem);
        $this->assertEquals(null,$cache->get('testkey2'));
    }
}
