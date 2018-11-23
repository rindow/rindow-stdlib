<?php
namespace Rindow\Stdlib;

use Iterator;
use ArrayAccess;

class ArrayAccessLimitIterator implements Iterator
{
	protected $collection;
	protected $offset;
	protected $count;
	protected $pos;
	protected $leftCount=0;
	protected $skipMax = 0;
	protected $exceptionClass;
	protected $presented;

	public function __construct(ArrayAccess $collection, $offset=0, $count=0 ,$skipMax=null, $exceptionClass=null)
	{
		$this->collection = $collection;
		$this->offset = $offset;
		$this->count = $count;
		if($skipMax)
			$this->skipMax = $skipMax;
		$this->exceptionClass = $exceptionClass;
		$this->rewind();
	}

    public function current()
    {
    	if(!$this->valid())
    		return null;
        return $this->collection->offsetGet($this->pos);
    }

    public function key()
    {
        return $this->pos;
    }

    public function next()
    {
    	if(!$this->valid())
    		return;
	    $this->pos++;
    	$this->leftCount--;
    }

    public function rewind()
    {
        $this->pos = $this->offset;
        $this->leftCount = $this->count;
    }

    public function valid()
    {
    	if($this->leftCount<=0)
    		return false;
    	return $this->searchPosition();
    }

    protected function searchPosition()
    {
    	if($this->presented===$this->pos)
    		return true;
    	$retry=$this->skipMax+1;
    	while($retry > 0) {
    		if($this->collection->offsetExists($this->pos)) {
    			$this->presented = $this->pos;
    			return true;
    		}
    		$retry--;
	    	$this->pos++;
    	}
    	$this->leftCount = 0;
    	if($this->exceptionClass) {
    		$class = $this->exceptionClass;
    		throw new $class('retry over to skip offset.');
    	}
    	return false;
    }
}