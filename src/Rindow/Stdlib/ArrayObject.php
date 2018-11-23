<?php
namespace Rindow\Stdlib;

use Iterator;
use Countable;
use ArrayAccess;

class ArrayObject implements Iterator,Countable,ArrayAccess
{
    protected $elements = array();
    
    protected function onAccess()
    {
    }

    public function offsetExists($name)
    {
        $this->onAccess();
        return isset($this->elements[$name]);
    }

    public function offsetGet($name)
    {
        $this->onAccess();
        if(isset($this->elements[$name]))
            return $this->elements[$name];
        else
            return null;
    }

    public function offsetSet($name, $value)
    {
        $this->onAccess();
        $this->elements[$name] = $value;
    }

    public function offsetUnset($name)
    {
        $this->onAccess();
        unset($this->elements[$name]);
    }

    public function toArray()
    {
        $this->onAccess();
        return $this->elements;
    }

    public function isEmpty()
    {
        $this->onAccess();
        return empty($this->elements);
    }

    public function count()
    {
        $this->onAccess();
        return count($this->elements);
    }

    public function current()
    {
        $this->onAccess();
        return current($this->elements);
    }

    public function key()
    {
        $this->onAccess();
        return key($this->elements);
    }

    public function next()
    {
        $this->onAccess();
        next($this->elements);
    }

    public function rewind()
    {
        $this->onAccess();
        reset($this->elements);
    }

    public function valid()
    {
        $this->onAccess();
        return (key($this->elements) !== null);
    }

    public function keys()
    {
        $this->onAccess();
        return array_keys($this->elements);
    }
}