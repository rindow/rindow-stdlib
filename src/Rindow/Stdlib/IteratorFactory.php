<?php
namespace Rindow\Stdlib;

use IteratorAggregate;
use Traversable;

class IteratorFactory implements IteratorAggregate
{
    protected $factory;

    public function __construct($factory)
    {
        if(!is_callable($factory))
            throw new \InvalidArgumentException('$factory must be callable.');
        $this->factory = $factory;
    }

    public function getIterator()
    {
        $iterator = call_user_func($this->factory);
        if(!($iterator instanceof Traversable) && !is_array($iterator))
            throw new \InvalidArgumentException('$factory must make Traversable.');
        return $iterator;
    }
}
