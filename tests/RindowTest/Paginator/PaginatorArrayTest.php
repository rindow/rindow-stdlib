<?php
namespace RindowTest\Paginator\PaginatorArrayTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Paginator\ArrayAdapter;
use Rindow\Stdlib\Paginator\Paginator;

class Test extends TestCase
{
    public static function setUpBeforeClass()
    {
    }
    public static function tearDownAfterClass()
    {
    }
    public function setUpArray($count)
    {
    	for ($i=0; $i < $count; $i++) { 
    		$array[] = 'item-'.$i;
    	}
    	return $array;
    }
    public function getItems($paginator)
    {
    	$results = array();
    	foreach ($paginator as $value) {
    		$results[] = $value;
    	}
    	return $results;
    }

    public function testNotHavePreviousPage()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(1,$paginator->getPage());         // default base number 1
    	$this->assertFalse($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(2,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-0','item-1','item-2','item-3','item-4'),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(10,$paginator->getLastPage());     // default base number 1
    }

    public function testHasPreviousPageOne()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(2);

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(2,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(3,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-5','item-6','item-7','item-8','item-9'),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(10,$paginator->getLastPage());     // default base number 1
    }

    public function testHasPreviousPageTwo()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(3);

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(3,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(2,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(4,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-10','item-11','item-12','item-13','item-14'),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(10,$paginator->getLastPage());     // default base number 1
    }

    public function testNotHaveNextPage()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(10);

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(10,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(9,$paginator->getPreviousPage());
    	$this->assertFalse($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(6,7,8,9,10),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-45','item-46','item-47','item-48'),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(10,$paginator->getLastPage());     // default base number 1
    }

    public function testHasNextPageOne()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(9);

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(9,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(8,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(6,7,8,9,10),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-40','item-41','item-42','item-43','item-44'),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(10,$paginator->getLastPage());     // default base number 1
    }

    public function testHasNextPageTwo()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(8);

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(8,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(7,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(9,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(6,7,8,9,10),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-35','item-36','item-37','item-38','item-39'),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(10,$paginator->getLastPage());     // default base number 1
    }

    public function testNotHavePreviousPageJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPageScrollingStyle('jumping');

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(1,$paginator->getPage());         // default base number 1
    	$this->assertFalse($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(8,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-0','item-1','item-2','item-3','item-4'),$this->getItems($paginator));
    }

    public function testHasPreviousPageOneJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(2);
    	$paginator->setPageScrollingStyle('jumping');

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(2,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(8,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-5','item-6','item-7','item-8','item-9'),$this->getItems($paginator));
    }

    public function testHasPreviousPageTwoJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(3);
    	$paginator->setPageScrollingStyle('jumping');

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(3,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(8,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-10','item-11','item-12','item-13','item-14'),$this->getItems($paginator));
    }

    public function testNotHaveNextPageJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(10);
    	$paginator->setPageScrollingStyle('jumping');

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(10,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(3,$paginator->getPreviousPage());
    	$this->assertFalse($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(6,7,8,9,10),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-45','item-46','item-47','item-48'),$this->getItems($paginator));
    }

    public function testHasNextPageOneJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(9);
    	$paginator->setPageScrollingStyle('jumping');

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(9,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(3,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(6,7,8,9,10),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-40','item-41','item-42','item-43','item-44'),$this->getItems($paginator));
    }

    public function testHasNextPageTwoJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setPage(8);
    	$paginator->setPageScrollingStyle('jumping');

    	$this->assertEquals(49,$paginator->getTotalItems());
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$this->assertEquals(8,$paginator->getPage());         // default base number 1
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(3,$paginator->getPreviousPage());
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(6,7,8,9,10),$paginator->getPagesInRange()); // default range 5
    	$this->assertEquals(array('item-35','item-36','item-37','item-38','item-39'),$this->getItems($paginator));
    }

    public function testItemSelection()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(10,$paginator->getTotalPages());

    	$paginator->setPage(1);
    	$this->assertEquals(array('item-0','item-1','item-2','item-3','item-4'),$this->getItems($paginator));
    	$paginator->setPage(2);
    	$this->assertEquals(array('item-5','item-6','item-7','item-8','item-9'),$this->getItems($paginator));
    	$paginator->setPage(9);
    	$this->assertEquals(array('item-40','item-41','item-42','item-43','item-44'),$this->getItems($paginator));
    	$paginator->setPage(10);
    	$this->assertEquals(array('item-45','item-46','item-47','item-48'),$this->getItems($paginator));

    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setItemMaxPerPage(2);
    	$this->assertEquals(25,$paginator->getTotalPages());

    	$paginator->setPage(1);
    	$this->assertEquals(array('item-0','item-1'),$this->getItems($paginator));
    	$paginator->setPage(2);
    	$this->assertEquals(array('item-2','item-3'),$this->getItems($paginator));
    	$paginator->setPage(24);
    	$this->assertEquals(array('item-46','item-47'),$this->getItems($paginator));
    	$paginator->setPage(25);
    	$this->assertEquals(array('item-48'),$this->getItems($paginator));


    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$paginator->setItemMaxPerPage(1);
    	$this->assertEquals(49,$paginator->getTotalPages());

    	$paginator->setPage(1);
    	$this->assertEquals(array('item-0'),$this->getItems($paginator));
    	$paginator->setPage(2);
    	$this->assertEquals(array('item-1'),$this->getItems($paginator));
    	$paginator->setPage(48);
    	$this->assertEquals(array('item-47'),$this->getItems($paginator));
    	$paginator->setPage(49);
    	$this->assertEquals(array('item-48'),$this->getItems($paginator));


    	$array = $this->setUpArray(50);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$paginator->setPage(10);
    	$this->assertEquals(array('item-45','item-46','item-47','item-48','item-49'),$this->getItems($paginator));

    	$array = $this->setUpArray(51);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(11,$paginator->getTotalPages());
    	$paginator->setPage(11);
    	$this->assertEquals(array('item-50'),$this->getItems($paginator));
    }

    public function testPageRangeSelection()
    {
    	$array = $this->setUpArray(100);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(20,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(5);

    	$paginator->setPage(1);
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange());
    	$paginator->setPage(2);
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange());
    	$paginator->setPage(3);
    	$this->assertEquals(array(1,2,3,4,5),$paginator->getPagesInRange());
    	$paginator->setPage(4);
    	$this->assertEquals(array(2,3,4,5,6),$paginator->getPagesInRange());
    	$paginator->setPage(5);
    	$this->assertEquals(array(3,4,5,6,7),$paginator->getPagesInRange());
    	$paginator->setPage(20);
    	$this->assertEquals(array(16,17,18,19,20),$paginator->getPagesInRange());
    	$paginator->setPage(19);
    	$this->assertEquals(array(16,17,18,19,20),$paginator->getPagesInRange());
    	$paginator->setPage(18);
    	$this->assertEquals(array(16,17,18,19,20),$paginator->getPagesInRange());
    	$paginator->setPage(17);
    	$this->assertEquals(array(15,16,17,18,19),$paginator->getPagesInRange());
    	$paginator->setPage(16);
    	$this->assertEquals(array(14,15,16,17,18),$paginator->getPagesInRange());

    	$array = $this->setUpArray(100);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(20,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(4);

    	$paginator->setPage(1);
    	$this->assertEquals(array(1,2,3,4),$paginator->getPagesInRange());
    	$paginator->setPage(2);
    	$this->assertEquals(array(1,2,3,4),$paginator->getPagesInRange());
    	$paginator->setPage(3);
    	$this->assertEquals(array(1,2,3,4),$paginator->getPagesInRange());
    	$paginator->setPage(4);
    	$this->assertEquals(array(2,3,4,5),$paginator->getPagesInRange());
    	$paginator->setPage(20);
    	$this->assertEquals(array(17,18,19,20),$paginator->getPagesInRange());
    	$paginator->setPage(19);
    	$this->assertEquals(array(17,18,19,20),$paginator->getPagesInRange());
    	$paginator->setPage(18);
    	$this->assertEquals(array(16,17,18,19),$paginator->getPagesInRange());

    	$array = $this->setUpArray(100);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(20,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(2);

    	$paginator->setPage(1);
    	$this->assertEquals(array(1,2),$paginator->getPagesInRange());
    	$paginator->setPage(2);
    	$this->assertEquals(array(1,2),$paginator->getPagesInRange());
    	$paginator->setPage(3);
    	$this->assertEquals(array(2,3),$paginator->getPagesInRange());
    	$paginator->setPage(20);
    	$this->assertEquals(array(19,20),$paginator->getPagesInRange());
    	$paginator->setPage(19);
    	$this->assertEquals(array(18,19),$paginator->getPagesInRange());


    	$array = $this->setUpArray(100);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(20,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(1);

    	$paginator->setPage(1);
    	$this->assertEquals(array(1),$paginator->getPagesInRange());
    	$paginator->setPage(2);
    	$this->assertEquals(array(2),$paginator->getPagesInRange());
    	$paginator->setPage(20);
    	$this->assertEquals(array(20),$paginator->getPagesInRange());
    	$paginator->setPage(19);
    	$this->assertEquals(array(19),$paginator->getPagesInRange());
    }

    public function testPreviousForSlidingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(5);
    	$paginator->setPageScrollingStyle('sliding');

    	$paginator->setPage(1);
    	$this->assertFalse($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$paginator->setPage(2);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$paginator->setPage(3);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(2,$paginator->getPreviousPage());
    	$paginator->setPage(10);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(9,$paginator->getPreviousPage());
    }

    public function testNextForSlidingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(5);
    	$paginator->setPageScrollingStyle('sliding');

    	$paginator->setPage(1);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(2,$paginator->getNextPage());
    	$paginator->setPage(10);
    	$this->assertFalse($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(9);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(8);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(9,$paginator->getNextPage());
    }

    public function testPreviousForJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(5);
    	$paginator->setPageScrollingStyle('jumping');

    	$paginator->setPage(1);
    	$this->assertFalse($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$paginator->setPage(2);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$paginator->setPage(6);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$paginator->setPage(7);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(2,$paginator->getPreviousPage());
    	$paginator->setPage(8);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(3,$paginator->getPreviousPage());
    	$paginator->setPage(9);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(3,$paginator->getPreviousPage());
    	$paginator->setPage(10);
    	$this->assertTrue($paginator->hasPreviousPage());
    	$this->assertEquals(3,$paginator->getPreviousPage());
    }

    public function testNextForJumpingScroll()
    {
    	$array = $this->setUpArray(49);
    	$adapter = new ArrayAdapter($array);
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(10,$paginator->getTotalPages());
    	$paginator->setPageRangeSize(5);
    	$paginator->setPageScrollingStyle('jumping');

    	$paginator->setPage(1);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(8,$paginator->getNextPage());
    	$paginator->setPage(2);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(8,$paginator->getNextPage());
    	$paginator->setPage(3);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(8,$paginator->getNextPage());
    	$paginator->setPage(4);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(9,$paginator->getNextPage());
    	$paginator->setPage(5);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(6);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(7);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(8);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(9);
    	$this->assertTrue($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    	$paginator->setPage(10);
    	$this->assertFalse($paginator->hasNextPage());
    	$this->assertEquals(10,$paginator->getNextPage());
    }

    public function testZeroItem()
    {
    	$adapter = new ArrayAdapter();
    	$paginator = new Paginator($adapter);
    	$this->assertEquals(0,$paginator->getTotalItems());
    	$this->assertEquals(0,$paginator->getTotalPages());

    	$this->assertEquals(1,$paginator->getPage());         // default base number 1
    	$this->assertFalse($paginator->hasPreviousPage());
    	$this->assertEquals(1,$paginator->getPreviousPage());
    	$this->assertFalse($paginator->hasNextPage());
    	$this->assertEquals(1,$paginator->getNextPage());     // default sliding
    	$this->assertEquals(array(1),$paginator->getPagesInRange());
    	$this->assertEquals(array(),$this->getItems($paginator));
        $this->assertEquals(1,$paginator->getFirstPage());     // default base number 1
        $this->assertEquals(1,$paginator->getLastPage());     // default base number 1
    }

    /**
     * @expectedException        Rindow\Stdlib\Paginator\Exception\InvalidArgumentException
     * @expectedExceptionMessage itemMaxPerPage must be positive number.
     */
    public function testSetItemMaxPerPageError()
    {
    	$paginator = new Paginator();
    	$paginator->setItemMaxPerPage(-1);
    }

    /**
     * @expectedException        Rindow\Stdlib\Paginator\Exception\InvalidArgumentException
     * @expectedExceptionMessage page must be greater then the basePageNumber or equal.
     */
    public function testSetPageError()
    {
    	$paginator = new Paginator();
    	$paginator->setPage(-1);
    }

    /**
     * @expectedException        Rindow\Stdlib\Paginator\Exception\DomainException
     * @expectedExceptionMessage adapter is not supplied.
     */
    public function testGetTotalItemsError()
    {
    	$paginator = new Paginator();
    	$paginator->getTotalItems();
    }

    /**
     * @expectedException        Rindow\Stdlib\Paginator\Exception\DomainException
     * @expectedExceptionMessage adapter is not supplied.
     */
    public function testGetIteratorError()
    {
    	$paginator = new Paginator();
    	$paginator->getIterator();
    }

    /**
     * @expectedException        Rindow\Stdlib\Paginator\Exception\DomainException
     * @expectedExceptionMessage Unkown page scrolling style.
     */
    public function testSetPageScrollingStyleError()
    {
    	$paginator = new Paginator();
    	$paginator->setPageScrollingStyle('unknown');
    }

    /**
     * @expectedException        Rindow\Stdlib\Paginator\Exception\DomainException
     * @expectedExceptionMessage PageRangeSize must be greater than zero.
     */
    public function testSetPageRangeSizeError()
    {
    	$paginator = new Paginator();
    	$paginator->setPageRangeSize(-1);
    }
}
