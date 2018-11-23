<?php
namespace RindowTest\Paginator\PaginatorOffsetAccessAdapterTest;

use PHPUnit\Framework\TestCase;
use ArrayObject;

use Rindow\Stdlib\Paginator\OffsetAccessAdapter;

class Test extends TestCase
{
    public function setUpArray($count)
    {
    	for ($i=0; $i < $count; $i++) { 
    		$array[] = 'item-'.$i;
    	}
    	return $array;
    }
    public function getItems($iterator)
    {
    	$results = array();
    	foreach ($iterator as $value) {
    		$results[] = $value;
    	}
    	return $results;
    }

	public function testNormal()
	{
		$arrayIterator = new ArrayObject($this->setUpArray(5));
		$adapter = new OffsetAccessAdapter($arrayIterator);

		$this->assertEquals(5,count($adapter));
		$this->assertEquals(array('item-2','item-3'),$this->getItems($adapter->getItems(2,2)));
	}

	public function testDirect()
	{
		$arrayIterator = new ArrayObject($this->setUpArray(5));
		$adapter = new OffsetAccessAdapter($arrayIterator);

		$this->assertEquals(array('item-0','item-1','item-2','item-3','item-4'),$this->getItems($adapter));
	}
}
