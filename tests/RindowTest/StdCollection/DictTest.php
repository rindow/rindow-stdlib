<?php
namespace RindowTest\StdCollection\DictTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Dict;
use Rindow\Stdlib\ArrayObject;
use ArrayObject as PHPArrayObject;

class Test extends TestCase
{
	public function testSet()
	{
		$dict = new Dict();
		$dict->set('a',1)->set('b',2)->set('c',3);
		$this->assertEquals(array('a'=>1,'b'=>2,'c'=>3),$dict->toArray());
	}

	public function testGet()
	{
		$dict = new Dict(array('a'=>1,'b'=>2,'c'=>3));
		$this->assertEquals(1,$dict->get('a'));
		$this->assertEquals(2,$dict->get('b'));
		$this->assertEquals(3,$dict->get('c'));
		$this->assertEquals(null,$dict->get('d'));
		$this->assertEquals('default',$dict->get('d','default'));
	}

	public function testHas()
	{
		$dict = new Dict(array('a'=>1,'b'=>null,'c'=>false));
		$this->assertTrue($dict->has('a'));
		$this->assertTrue($dict->has('b'));
		$this->assertTrue($dict->has('c'));
		$this->assertFalse($dict->has('d'));

		$this->assertFalse(isset($dict['b']));
		$this->assertTrue(isset($dict['c']));
	}

	public function testDelete()
	{
		$dict = new Dict(array('a'=>1,'b'=>2,'c'=>3));
		$this->assertTrue($dict->has('a'));
		$dict->delete('a');
		$this->assertFalse($dict->has('a'));

		$this->assertTrue(isset($dict['b']));
		unset($dict['b']);
		$this->assertFalse(isset($dict['b']));

		$this->assertEquals(array('c'=>3),$dict->toArray());
	}

	public function testClear()
	{
		$dict = new Dict(array('a'=>1,'b'=>2,'c'=>3));
		$dict->clear();
		$this->assertEquals(array(),$dict->toArray());
	}

	public function testPop()
	{
		$dict = new Dict(array('a'=>1,'b'=>2,'c'=>3));
		list($key,$value) = $dict->pop();
		$this->assertEquals('c',$key);
		$this->assertEquals(3,$value);
		$this->assertEquals(array('a'=>1,'b'=>2),$dict->toArray());
	}

	public function testSetDefault()
	{
		$dict = new Dict(array('a'=>1,'b'=>null));
		$this->assertEquals(1,$dict->setDefault('a','default'));
		$this->assertEquals(1,$dict->get('a'));
		$this->assertEquals(null,$dict->setDefault('b','default'));
		$this->assertEquals(null,$dict->get('b'));
		$this->assertEquals('default',$dict->setDefault('c','default'));
		$this->assertEquals('default',$dict->get('c'));

		$this->assertEquals(array('a'=>1,'b'=>null,'c'=>'default'),$dict->toArray());
	}

	public function testValues()
	{
		$dict = new Dict(array('a'=>1,'b'=>2,'c'=>3));
		$this->assertEquals(array(1,2,3),$dict->values());
	}

	public function testConstructor()
	{
		$object = new PHPArrayObject(array('a'=>1,'b'=>2,'c'=>3));
		$object = new PHPArrayObject($object);
		$dict = new Dict($object);
		$this->assertEquals(array('a'=>1,'b'=>2,'c'=>3),$dict->toArray());

		$object = new ArrayObject();
		$object['a'] = 1;
		$object['b'] = 2;
		$object['c'] = 3;
		$dict = new Dict($object);
		$this->assertEquals(array('a'=>1,'b'=>2,'c'=>3),$dict->toArray());

		$object = new Dict(array('a'=>1,'b'=>2,'c'=>3));
		$dict = new Dict($object);
		$this->assertEquals(array('a'=>1,'b'=>2,'c'=>3),$dict->toArray());
	}
}
