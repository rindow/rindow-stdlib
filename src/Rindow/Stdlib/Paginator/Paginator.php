<?php
namespace Rindow\Stdlib\Paginator;

use IteratorAggregate;
use Rindow\Stdlib\Paginator\Exception;

class Paginator implements IteratorAggregate
{
	protected $adapter;
	protected $itemMaxPerPage=5;
	protected $basePageNumber=1;
	protected $page=0;
	protected $totalItems;
	protected $totalPages;
    protected $pageRangeSize=5;
    protected $pageScrollingStyle='sliding';

	public function __construct(PaginatorAdapter $adapter=null)
	{
		$this->adapter = $adapter;
	}

    public function setAdapter(PaginatorAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

	public function setItemMaxPerPage($max)
	{
		if(!is_numeric($max) || $max <= 0)
			throw new Exception\InvalidArgumentException('itemMaxPerPage must be positive number.');
		$this->itemMaxPerPage = $max;
		return $this;
	}

    public function setBasePageNumber($page)
    {
    	$this->basePageNumber = $page;
		return $this;
    }

	public function setPage($page)
	{
		$page = $page - $this->basePageNumber;
		if(!is_numeric($page) || $page < 0)
			throw new Exception\InvalidArgumentException('page must be greater then the basePageNumber or equal.');
		$this->page = $page;
		return $this;
	}

	public function getPage()
	{
		return $this->page + $this->basePageNumber;
	}

    public function getTotalItems()
    {
    	if($this->totalItems!==null)
    		return  $this->totalItems;
        if($this->adapter===null)
            throw new Exception\DomainException('adapter is not supplied.');
        return $this->totalItems = count($this->adapter);
    }

    public function getTotalPages()
    {
    	if($this->totalPages!==null)
    		return $this->totalPages;
    	$itemCount = $this->getTotalItems();
    	return $this->totalPages = ceil($itemCount / $this->itemMaxPerPage);
    }

    public function setPageScrollingStyle($style)
    {
        $style = strtolower($style);
        if($style!=='jumping' && $style!=='sliding')
            throw new Exception\DomainException('Unkown page scrolling style.');
        $this->pageScrollingStyle = $style;
        return $this;
    }

    public function hasPreviousPage()
    {
    	return $this->page > 0 ;
    }

    public function getPreviousPage()
    {
    	if($this->hasPreviousPage()) {
            if($this->pageScrollingStyle==='sliding') {
                $page = $this->page - 1;
            } else {
                list($start,$end) = $this->calcPagesInRange();
                $page = $start - $this->pageRangeSize + floor($this->pageRangeSize/2);
            }
            if($page<0)
                $page = 0;
            return $page + $this->basePageNumber;
    	} else {
    		return $this->basePageNumber;
    	}
    }

    public function hasNextPage()
    {
    	return $this->page+1 < $this->getTotalPages();
    }

    public function getNextPage()
    {
    	if($this->hasNextPage()) {
            if($this->pageScrollingStyle==='sliding') {
                $page = $this->page + 1;
            } else {
                list($start,$end) = $this->calcPagesInRange();
                $page = $start + $this->pageRangeSize + floor($this->pageRangeSize/2);
            }
            if($page >= $this->getTotalPages())
                $page = $this->getTotalPages() - 1;
            return $page + $this->basePageNumber;
    	} else {
    		if($this->getTotalPages())
	    		return $this->getTotalPages()-1 + $this->basePageNumber;
	    	else
	    		return $this->basePageNumber;
    	}
    }

    public function setPageRangeSize($size)
    {
        if($size<=0)
            throw new Exception\DomainException('PageRangeSize must be greater than zero.');
        $this->pageRangeSize = $size;
        return $this;
    }

    protected function calcPagesInRange()
    {
        if($this->getTotalPages()==0)
            return array(null,null);
        $start = $this->page - floor($this->pageRangeSize/2);
        if($start<0)
            $start = 0;
        $end = $start+$this->pageRangeSize-1;
        if($end >= $this->getTotalPages()) {
            $end = $this->getTotalPages()-1;
            $start = $end-$this->pageRangeSize+1;
            if($start<0)
                $start = 0;
        }
        return array(intval($start),intval($end));
    }

    public function getPagesInRange()
    {
        list($start,$end) = $this->calcPagesInRange();
        if($start===null && $end===null)
            return array($this->basePageNumber);
        return range($start+$this->basePageNumber , $end+$this->basePageNumber);
    }

    public function getFirstPage()
    {
        return $this->basePageNumber;
    }

    public function getLastPage()
    {
        $page = $this->getTotalPages()-1;
        if($page<0)
            $page=0;
        return $page+$this->basePageNumber;
    }

	public function getIterator()
	{
        if($this->adapter===null)
            throw new Exception\DomainException('adapter is not supplied.');
        return $this->adapter->getItems(
            $this->page*$this->itemMaxPerPage,
            $this->itemMaxPerPage);
	}
}