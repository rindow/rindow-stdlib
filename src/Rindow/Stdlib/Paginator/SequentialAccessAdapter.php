<?php
namespace Rindow\Stdlib\Paginator;

use Iterator;
use LimitIterator;
use IteratorAggregate;
use Countable;

class SequentialAccessAdapter implements PaginatorAdapter,IteratorAggregate
{
    protected $collection;
    protected $itemCount;

    function __construct(Iterator $collection)
    {
        if(!($collection instanceof Countable))
            throw new Exception\InvalidArgumentException('collection type must implement "Countable".');
        $this->collection = $collection;
    }

    public function count()
    {
        if($this->itemCount !== null)
            $this->itemCount;
        return $this->itemCount = count($this->collection);
    }

    public function getItems($offset, $itemMaxPerPage)
    {
        return new LimitIterator($this->collection, $offset, $itemMaxPerPage);
    }

    public function getIterator()
    {
        return $this->collection;
    }
}