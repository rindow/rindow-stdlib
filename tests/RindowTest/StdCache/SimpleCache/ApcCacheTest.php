<?php
namespace RindowTest\Stdlib\Cache\SimpleCache\ApcCacheTest;

use PHPUnit\Framework\TestCase;
use ArrayObject;

// Test Target Classes
use Rindow\Stdlib\Cache\SimpleCache\ApcCache;

class Test extends TestCase
{
    public static $skip = false;

    public static function setUpBeforeClass()
    {
        if(!extension_loaded('apcu')&&!extension_loaded('apc')) {
            self::$skip = 'apc or apc extension not loaded.';
            return;
        }
    }

    public static function tearDownAfterClass()
    {
    }

    public function setUp()
    {
        if(self::$skip) {
            $this->markTestSkipped(self::$skip);
            return;
        }
        if(extension_loaded('apcu')) {
            apcu_clear_cache();
            //echo 'apcu';
        } elseif(extension_loaded('apc')) {
            //echo 'apc';
            apc_clear_cache('user');
        }
    }

    public function getCache($config=null)
    {
        $cache = new ApcCache();
        if($config)
            $cache->setConfig($config);
        return $cache;
    }

    public function isDisabledCli()
    {
        if (ini_get('apc.enable_cli')) {
            return false;
        }
        $this->markTestSkipped('apc.enable_cli is disabled');
        return true;
    }

    public function testSingleNormal()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $this->assertTrue($cache->isReady());

        $this->assertTrue($cache->set('a','A'));
        $this->assertTrue($cache->set('b','B'));

        $this->assertTrue($cache->has('a'));
        $this->assertTrue($cache->has('b'));
        $this->assertFalse($cache->has('c'));

        $this->assertEquals('A',$cache->get('a','default'));
        $this->assertEquals('B',$cache->get('b','default'));
        $this->assertEquals('default',$cache->get('c','default'));

        $this->assertTrue($cache->delete('a'));
        $this->assertTrue($cache->delete('b'));
        $this->assertFalse($cache->delete('c'));

        $this->assertFalse($cache->has('a'));
        $this->assertFalse($cache->has('b'));
        $this->assertFalse($cache->has('c'));

        // *** CAUTION ***
        // ttl test can not be done with phpunit
        //
        //$cache->set('a-Ttl','A',1);
        //$this->assertTrue($cache->has('a-Ttl'));
        //sleep(2);
        //$this->assertFalse($cache->has('a-Ttl'));
    }

    public function testMultipleNormal()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $values = array('a'=>'A','b'=>'B','c'=>'C');
        $this->assertTrue($cache->setMultiple($values));

        $keys = array('a','b','c','d');
        $this->assertEquals(array('a'=>'A','b'=>'B','c'=>'C','d'=>'default'),
            $cache->getMultiple($keys,'default'));

        $keys = array('b','c');
        $this->assertTrue($cache->deleteMultiple($keys));

        $keys = array('a','b','c','d');
        $this->assertEquals(array('a'=>'A','b'=>'default','c'=>'default','d'=>'default'),
            $cache->getMultiple($keys,'default'));

        $this->assertTrue($cache->has('a'));
        $keys = array('b','c','a');
        $this->assertFalse($cache->deleteMultiple($keys));
        $this->assertFalse($cache->has('a'));

        // *** CAUTION ***
        // ttl test can not be done with phpunit
        //
    }

    public function testMultipleTraversableNormal()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $values = new ArrayObject(array('a'=>'A','b'=>'B','c'=>'C'));
        $this->assertTrue($cache->setMultiple($values));

        $keys = new ArrayObject(array('a','b','c','d'));
        $this->assertEquals(array('a'=>'A','b'=>'B','c'=>'C','d'=>'default'),
            $cache->getMultiple($keys,'default'));

        $keys = new ArrayObject(array('b','c'));
        $this->assertTrue($cache->deleteMultiple($keys));
    }

    public function testValueType()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $num = 123;
        $string = 'string';
        $array = array('a'=>'A','b'=>'B','c'=>'C');
        $object = (object)$array;
        $objectClone = clone $object;

        $this->assertTrue($cache->set('num_key',$num));
        $this->assertTrue($cache->set('string_key',$string));
        $this->assertTrue($cache->set('array_key',$array));
        $this->assertTrue($cache->set('object_key',$object));

        $this->assertEquals($num,$cache->get('num_key'));
        $this->assertEquals($string,$cache->get('string_key'));
        $this->assertEquals($array,$cache->get('array_key'));
        $this->assertEquals($objectClone,$cache->get('object_key'));
    }

    public function testClear()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $this->assertTrue($cache->set('a','A'));
        $this->assertTrue($cache->clear());
        $this->assertFalse($cache->has('a'));
        $this->assertEquals('default',$cache->get('a','default'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidGetKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->get(array('a'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidHasKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->has(array('a'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidSetKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->set(array('a'),'a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidDeleteKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->delete(array('a'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Keys must be array or Traversable.
     */
    public function testInvalidGetMultipleKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->getMultiple('a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidGetMultipleKey2()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $keys = array('a',array('b'));
        $cache->getMultiple($keys);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Values must be array or Traversable.
     */
    public function testInvalidSetMultipleKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->setMultiple('a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Keys must be array or Traversable.
     */
    public function testInvalidDeleteMultipleKey()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $cache->deleteMultiple('a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidDeleteMultipleKey2()
    {
        if($this->isDisabledCli())
            return;

        $cache = $this->getCache();

        $keys = array('a',array('b'));
        $cache->deleteMultiple($keys);
    }

    public function testCliDisabled()
    {
        if (ini_get('apc.enable_cli')) {
            $this->markTestSkipped('apc.enable_cli is enabled');
            return;
        }

        $cache = $this->getCache();
        $this->assertFalse($cache->isReady());
    }

    /**
     * @expectedException        Rindow\Stdlib\Cache\Exception\CacheException
     * @expectedExceptionMessage apc or apcu extension is not loaded. Or apc.cli_enable is 0
     */
    public function testCliDisabledError()
    {
        if (ini_get('apc.enable_cli')) {
            $this->markTestSkipped('apc.enable_cli is enabled');
            return;
        }

        $cache = $this->getCache();
        $this->assertFalse($cache->isReady());
        $cache->set('A','a');
    }

}
