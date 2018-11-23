<?php
namespace RindowTest\StdCache\MemcacheCacheTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Cache\MemcacheCache;
use Rindow\Stdlib\Cache\CacheChain;

class Test extends TestCase
{
    public static $skip = false;
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('memcache')) {
            self::$skip = true;
            return;
        }
        try {
            $memcache = @memcache_connect('localhost');
            if(!$memcache)
                self::$skip = true;
        } catch(\Exception $e) {
            self::$skip = true;
            return;
        }
    }

    public function setUp()
    {
        if(self::$skip) {
            $this->markTestSkipped();
            return;
        }
    }

    /**
     * @requires extension memcache
     */
    public function testMemcacheStore()
    {
        $cache = new MemcacheCache(RINDOW_TEST_CACHE.'/cache');

        $cache['a'] = 'A';
        $cache['b\\b'] = 'B';

        $this->assertEquals('A',$cache['a']);
        $this->assertEquals('B',$cache['b\\b']);

        unset($cache['a']);
        $this->assertFalse(isset($cache['a']));
        $this->assertFalse($cache->containsKey('a'));
        unset($cache['b\\b']);

        $cache2t = new MemcacheCache(RINDOW_TEST_CACHE.'/cache2',1);

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
     * @requires extension memcache
     */
    public function testMemcacheGetAndPutAndRemoveStyle()
    {
        $cache = new MemcacheCache(RINDOW_TEST_CACHE.'/cache');

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
     * @requires extension memcache
     */
    public function testApcGetWithCallback()
    {
        $phpunit = $this;
        $cache = new MemcacheCache(RINDOW_TEST_CACHE.'/cache');
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

        $cache = new MemcacheCache(RINDOW_TEST_CACHE.'/cache');
        $this->assertEquals(null,$cache->get('testkey2'));
    }
}
