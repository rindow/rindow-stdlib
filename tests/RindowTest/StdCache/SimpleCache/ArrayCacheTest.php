<?php
namespace RindowTest\Stdlib\Cache\SimpleCache\ArrayCacheTest;

use PHPUnit\Framework\TestCase;
use ArrayObject;

// Test Target Classes
use Rindow\Stdlib\Cache\SimpleCache\ArrayCache;

class Test extends TestCase
{
    public static $skip = false;

    public static function setUpBeforeClass()
    {
    }

    public static function tearDownAfterClass()
    {
    }

    public function setUp()
    {
        if(self::$skip) {
            $this->markTestSkipped('arraycache not ready.');
            return;
        }
    }

    public function getCache($config=null)
    {
        $cache = new ArrayCache();
        if($config)
            $cache->setConfig($config);
        return $cache;
    }

    public function testSingleNormal()
    {
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
    }

    public function testMultipleNormal()
    {
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
        $cache = $this->getCache();

        $cache->get(array('a'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidHasKey()
    {
        $cache = $this->getCache();

        $cache->has(array('a'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidSetKey()
    {
        $cache = $this->getCache();

        $cache->set(array('a'),'a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidDeleteKey()
    {
        $cache = $this->getCache();

        $cache->delete(array('a'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Keys must be array or Traversable.
     */
    public function testInvalidGetMultipleKey()
    {
        $cache = $this->getCache();

        $cache->getMultiple('a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidGetMultipleKey2()
    {
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
        $cache = $this->getCache();

        $cache->setMultiple('a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Keys must be array or Traversable.
     */
    public function testInvalidDeleteMultipleKey()
    {
        $cache = $this->getCache();

        $cache->deleteMultiple('a');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Key must be string.
     */
    public function testInvalidDeleteMultipleKey2()
    {
        $cache = $this->getCache();

        $keys = array('a',array('b'));
        $cache->deleteMultiple($keys);
    }
}
