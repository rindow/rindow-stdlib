<?php
namespace Rindow\Stdlib\Paginator;

use ArrayIterator;
use IteratorAggregate;

class ArrayAdapter implements PaginatorAdapter,IteratorAggregate
{
    protected $array;

    public function __construct(array $array=array())
    {
        $this->array = $array;
    }

    public function count()
    {
        return count($this->array);
    }

    public function getItems($offset, $itemMaxPerPage)
    {
        return new ArrayIterator(array_slice($this->array, $offset, $itemMaxPerPage));
    }

    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }
}