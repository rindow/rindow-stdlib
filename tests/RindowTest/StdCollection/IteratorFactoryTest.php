<?php
namespace RindowTest\StdCollection\IteratorFactoryTest;

use PHPUnit\Framework\TestCase;
use ArrayIterator;
use Rindow\Stdlib\IteratorFactory;

class Test extends TestCase
{
	public function testNormal()
	{
		$factory = function() {
			$array = array('a','b','c');
			return new ArrayIterator($array);
		};
		$iterator = new IteratorFactory($factory);
		$result = array();
		foreach ($iterator as $key => $value) {
			$result[$key] = $value;
		}
		$this->assertEquals(array('a','b','c'),$result);
	}

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage $factory must be callable.
     */
	public function testInvalidType()
	{
		$iterator = new IteratorFactory('a');
	}
}
