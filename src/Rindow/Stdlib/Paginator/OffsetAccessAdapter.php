<?php
namespace Rindow\Stdlib\Paginator;

use Iterator;
use IteratorAggregate;
use ArrayAccess;
use Countable;
use Rindow\Stdlib\ArrayAccessLimitIterator;

class OffsetAccessAdapter implements PaginatorAdapter,IteratorAggregate
{
    protected $collection;
    protected $itemCount;
    protected $skipMax;
    protected $exceptionClass;

    public function __construct(ArrayAccess $collection, $skipMax=null, $exceptionClass=null)
    {
        if(!($collection instanceof Countable))
            throw new Exception\InvalidArgumentException('collection type must implement "Countable".');
        $this->collection = $collection;
        $this->skipMax = $skipMax;
        $this->exceptionClass = $exceptionClass;
    }

    public function count()
    {
        if($this->itemCount !== null)
            $this->itemCount;
        return $this->itemCount = count($this->collection);
    }

    public function getItems($offset, $itemMaxPerPage)
    {
        return new ArrayAccessLimitIterator($this->collection, $offset, $itemMaxPerPage, $this->skipMax, $this->exceptionClass);
    }

    public function getIterator()
    {
        return $this->collection;
    }
}