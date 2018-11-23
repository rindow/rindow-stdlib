<?php
namespace Rindow\Stdlib;

use ArrayObject as PHPArrayObject;
use InvalidArgumentException;

class Dict extends ArrayObject
{
    public function __construct($array=null)
    {
        if($array==null)
            ;
        elseif(is_array($array))
            $this->elements = $array;
        elseif($array instanceof ArrayObject)
            $this->elements = $array->toArray();
        elseif($array instanceof PHPArrayObject)
            $this->elements = $array->getArrayCopy();
        else
            throw new InvalidArgumentException('array must be "array" or "ArrayObject"');
    }

    public function set($name,$value)
    {
        $this->offsetSet($name, $value);
        return $this;
    }

    public function get($name,$default=null)
    {
        if($this->has($name))
            return $this->offsetGet($name);
        return $default;
    }

    public function has($name)
    {
        $this->onAccess();
        return array_key_exists($name, $this->elements);
    }

    public function delete($name)
    {
        $this->offsetUnset($name);
        return $this;
    }

    public function clear()
    {
        $this->onAccess();
        $this->elements = array();
        return $this;
    }

    public function pop($name=null,$default=null)
    {
        if($name!==null) {
            $value = $this->get($name,$default);
            unset($this->elements[$name]);
            return $value;
        }
        $this->onAccess();
        end($this->elements);
        $key = key($this->elements);
        $value = current($this->elements);
        unset($this->elements[$key]);
        return array($key,$value);
    }

    public function setDefault($name,$default=null)
    {
        if($this->has($name))
            return $this->offsetGet($name);
        $this->set($name,$default);
        return $default;
    }

    public function values()
    {
        $this->onAccess();
        return array_values($this->elements);
    }

    public function setAll(array $elements)
    {
        $this->onAccess();
        $this->elements = $elements;
    }

    public function getAll()
    {
        return $this->toArray();
    }
}