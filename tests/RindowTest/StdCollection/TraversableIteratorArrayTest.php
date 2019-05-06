<?php
namespace RindowTest\StdCollection\TraversableIteratorArrayTest;

use PHPUnit\Framework\TestCase;
use ArrayIterator;
use IteratorIterator;
use IteratorAggregate;
use Rindow\Stdlib\IteratorInterceptor;
use Exception;

class TestException extends Exception
{}

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
            throw new TestException("Not yet allowed");
            
        return new ArrayIterator($this->array);
    }
}

class Test extends TestCase
{
    public static function setUpBeforeClass()
    {
    }
    public function setUp()
    {
        $this->array = array();
        $this->array[0] = array('id'=>1,'name'=>'foo1');
        $this->array[1] = array('id'=>2,'name'=>'foo2');
    }
    public function getClient()
    {
        $client = new ArrayIterator($this->array);
        return $client;
    }
    public function getIterator($client=null)
    {
        if($client==null)
            $client = $this->getClient();
        return $client;
    }

    public function testIterator()
    {
        $client = $this->getClient();
        $stmt = $this->getIterator($client);
        $iterator = new IteratorIterator($stmt);

        $results = array();
        foreach ($iterator as $value) {
            $results[] = $value;
        }
        $this->assertCount(2,$results);

        $stmt = $this->getIterator($client);
        $iterator = new IteratorIterator($stmt);
        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>1,'name'=>'foo1'),$iterator->current());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals(array('id'=>2,'name'=>'foo2'),$iterator->current());
        $iterator->next();
        $this->assertFalse($iterator->valid());

        $iterator->rewind();

        // ***** it is able to rewind ******
        $this->assertTrue($iterator->valid());
    }

    public function testNestIterator()
    {
        $client = $this->getClient();
        $stmt = $this->getIterator($client);
        $iterator = new IteratorIterator($stmt);
        $iterator = new IteratorIterator($iterator);

        $results = array();
        foreach ($iterator as $value) {
            $results[] = $value;
        }
        $this->assertCount(2,$results);
    }

    /**
     * @expectedException        RindowTest\StdCollection\TraversableIteratorArrayTest\TestException
     * @expectedExceptionMessage Not yet allowed
     */
    public function testAggregator()
    {
        $array = array(array('id'=>1,'name'=>'foo1'),array('id'=>2,'name'=>'foo2'));
        $stmt = new TestAggregator($array);
        $iterator = new IteratorIterator($stmt);
    }
}
