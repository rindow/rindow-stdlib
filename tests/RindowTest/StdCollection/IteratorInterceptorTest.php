<?php
namespace RindowTest\StdCollection\IteratorInterceptorTest;

use PHPUnit\Framework\TestCase;
use ArrayIterator;
use IteratorIterator;
use IteratorAggregate;
use Rindow\Stdlib\IteratorInterceptor;

class TestAggregator implements IteratorAggregate
{
    protected $array;
    protected $allowed;
    public function __construct($array)
    {
        $this->array = $array;
    }
    public function allow()
    {
        $this->allowed = true;
    }
    public function getIterator()
    {
        if(!$this->allowed)
            throw new \Exception("Not yet allowed");
            
        return new ArrayIterator($this->array);
    }
}

class Test extends TestCase
{
    public function testArray()
    {
        $array = array(array('id'=>1,'name'=>'foo1'),array('id'=>2,'name'=>'foo2'));
        $iterator = new IteratorInterceptor($array);
        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>1,'name'=>'foo1'),$iterator->current());
        $this->assertEquals(0,$iterator->key());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>2,'name'=>'foo2'),$iterator->current());
        $this->assertEquals(1,$iterator->key());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    public function testIterator()
    {
        $array = array(array('id'=>1,'name'=>'foo1'),array('id'=>2,'name'=>'foo2'));
        $stmt = new ArrayIterator($array);
        $iterator = new IteratorInterceptor($stmt);
        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>1,'name'=>'foo1'),$iterator->current());
        $this->assertEquals(0,$iterator->key());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>2,'name'=>'foo2'),$iterator->current());
        $this->assertEquals(1,$iterator->key());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    public function testLazyAggregate()
    {
        $array = array(array('id'=>1,'name'=>'foo1'),array('id'=>2,'name'=>'foo2'));
        $stmt = new TestAggregator($array);
        $iterator = new IteratorInterceptor($stmt);
        $stmt->allow();
        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>1,'name'=>'foo1'),$iterator->current());
        $this->assertEquals(0,$iterator->key());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>2,'name'=>'foo2'),$iterator->current());
        $this->assertEquals(1,$iterator->key());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    public function testFilters()
    {
        $array = array(array('id'=>1,'name'=>'foo1'),array('id'=>2,'name'=>'foo2'));
        $stmt = new TestAggregator($array);
        $iterator = new IteratorInterceptor($stmt);
        $iterator->addFilter(function($data) {
            $data['name'] = 'Modified '.$data['name'];
            return $data;
        });
        $iterator->addFilter(function($data) {
            $id = $data['id'];
            unset($data['id']);
            $newData = array('num'=>$id);
            $newData = array_merge($newData,$data);
            return $newData;
        });
        $iterator->addKeyFilter(function($key){
            return $key+100;
        });
        $stmt->allow();
        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('num'=>1,'name'=>'Modified foo1'),$iterator->current());
        $this->assertEquals(100,$iterator->key());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('num'=>2,'name'=>'Modified foo2'),$iterator->current());
        $this->assertEquals(101,$iterator->key());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage $iterator must be Traversable or array.
     */
    public function testInvalidType()
    {
        $object = 'abc';
        $iterator = new IteratorInterceptor($object);
    }
}
