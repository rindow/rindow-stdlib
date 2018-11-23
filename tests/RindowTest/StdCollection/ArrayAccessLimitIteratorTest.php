<?php
namespace RindowTest\StdCollection\ArrayAccessLimitIteratorTest;

use PHPUnit\Framework\TestCase;
use stdClass;
use ArrayObject;

// Test Target Class
use Rindow\Stdlib\ArrayAccessLimitIterator;

class Test extends TestCase
{
	public function testNormal()
	{
		$collection = new ArrayObject(array(
			-2 => '@-2',
			-1 => '@-1',
			 0 => '@0',
			 1 => '@+1',
			 2 => '@+2',
			 3 => '@+3',
		));
		$iterator = new ArrayAccessLimitIterator($collection,-1,3);
		foreach ($iterator as $key => $value) {
			$result[$key]=$value;
		}
		$this->assertEquals(array(-1=>'@-1',0=>'@0',1=>'@+1'),$result);
	}

	public function testOverRun()
	{
		$collection = new ArrayObject(array(
			-2 => '@-2',
			-1 => '@-1',
			 0 => '@0',
			 1 => '@+1',
			 2 => '@+2',
			 3 => '@+3',
		));
		$iterator = new ArrayAccessLimitIterator($collection,3,3);
		foreach ($iterator as $key => $value) {
			$result[$key]=$value;
		}
		$this->assertEquals(array(3=>'@+3'),$result);
	}

	public function testSkip()
	{
		$collection = new ArrayObject(array(
			-2 => '@-2',
			-1 => '@-1',
			 0 => '@0',
			// Skip 1 => '@+1',
			 2 => '@+2',
			 3 => '@+3',
		));
		$iterator = new ArrayAccessLimitIterator($collection,-1,3,1);
		foreach ($iterator as $key => $value) {
			$result[$key]=$value;
		}
		$this->assertEquals(array(-1=>'@-1',0=>'@0',2=>'@+2'),$result);
	}

	public function testSkipAndOverRun()
	{
		$collection = new ArrayObject(array(
			-2 => '@-2',
			-1 => '@-1',
			 0 => '@0',
			 1 => '@+1',
			// skip 2 => '@+2',
			 3 => '@+3',
		));
		$iterator = new ArrayAccessLimitIterator($collection,1,3,1);
		foreach ($iterator as $key => $value) {
			$result[$key]=$value;
		}
		$this->assertEquals(array(1=>'@+1',3=>'@+3'),$result);
	}

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage retry over to skip offset.
     */
	public function testOverRunWithThrowException()
	{
		$collection = new ArrayObject(array(
			-2 => '@-2',
			-1 => '@-1',
			 0 => '@0',
			 1 => '@+1',
			// skip 2 => '@+2',
			 3 => '@+3',
		));
		$iterator = new ArrayAccessLimitIterator($collection,1,3,1,'RuntimeException');
		foreach ($iterator as $key => $value) {
			$result[$key]=$value;
		}
	}
}