<?php
namespace Rindow\Stdlib;

use Traversable;
use Iterator;
use IteratorAggregate;
use IteratorIterator;
use ArrayIterator;

class IteratorInterceptor implements Iterator
{
    protected $original;
    protected $iterator;
    protected $filters = array();
    protected $keyFilters = array();

    public function __construct($iterator)
    {
        if(!($iterator instanceof Traversable) && !is_array($iterator))
            throw new \InvalidArgumentException('$iterator must be Traversable or array.');
        $this->original = $iterator;
    }

    public function addFilter($filter)
    {
        if(!is_callable($filter))
            throw new \InvalidArgumentException('filter must be callable.');
        $this->filters[] = $filter;
        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function addKeyFilter($keyFilter)
    {
        if(!is_callable($keyFilter))
            throw new \InvalidArgumentException('filter must be callable.');
        $this->keyFilters[] = $keyFilter;
        return $this;
    }

    public function getKeyFilters()
    {
        return $this->keyFilters;
    }

    public function setkeyFilters(array $keyFilters)
    {
        $this->keyFilters = $keyFilters;
    }

    protected function normalizationIterator()
    {
        if($this->iterator)
            return $this->iterator;

        $original = $this->original;
        while($original instanceof IteratorAggregate) {
            $original = $original->getIterator();
        }

        if($original instanceof Iterator)
            $this->iterator = $original;
        elseif($original instanceof Traversable)
            $this->iterator = new IteratorIterator($original);
        elseif(is_array($original))
            $this->iterator = new ArrayIterator($original);
        else
            throw new \InvalidArgumentException('$iterator must be Traversable or array.');
        return $this->iterator;
    }

    public function current()
    {
        $data = $this->normalizationIterator()->current();
        foreach ($this->filters as $filter) {
            $data = call_user_func($filter,$data);
        }
        return $data;
    }

    public function key()
    {
        $key = $this->normalizationIterator()->key();
        foreach ($this->keyFilters as $keyFilter) {
            $key = call_user_func($keyFilter,$key);
        }
        return $key;
    }

    public function next()
    {
        return $this->normalizationIterator()->next();
    }

    public function rewind()
    {
        return $this->normalizationIterator()->rewind();
    }

    public function valid()
    {
        return $this->normalizationIterator()->valid();
    }
}
