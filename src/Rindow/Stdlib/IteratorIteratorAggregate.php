<?php
namespace Rindow\Stdlib;

use Iterator;
use Traversable;
use IteratorAggregate;
use InvalidArgumentException;

/**
 * Required PHP 5.5.0 or Later
 */
class IteratorIteratorAggregate implements IteratorAggregate
{
    private $_innerIteratorValues;

    public function __construct($iterator)
    {
        if(!is_array($iterator) && !($iterator instanceof Traversable))
            throw new InvalidArgumentException('Iterator must be array or Traversable');
            
        $this->_innerIteratorValues['iterator'] = $iterator;
        $this->_innerIteratorValues['key'] = null;
        $this->_innerIteratorValues['current'] = null;
        $this->_innerIteratorValues['valid'] = false;
    }

    public function getIterator()
    {
        $this->rewind();
        foreach ($this->_innerIteratorValues['iterator'] as $key => $value) {
            $this->_innerIteratorValues['key'] = $key;
            $this->_innerIteratorValues['current'] = $value;
            if(!$this->valid())
                break;
            $value = $this->current();
            $key = $this->key();
            yield $key => $value;
            $this->next();
        }
        $this->_innerIteratorValues['valid'] = false;
    }

    protected function current()
    {
        return $this->_innerIteratorValues['current'];
    }

    protected function key()
    {
        return $this->_innerIteratorValues['key'];
    }

    protected function rewind()
    {
        $this->_innerIteratorValues['valid'] = true;
    }

    protected function next()
    {
    }

    protected function valid()
    {
        return $this->_innerIteratorValues['valid'];
    }
}