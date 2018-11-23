<?php
namespace Rindow\Stdlib;
use Iterator;
use Countable;
use ArrayAccess;

class ListCollection implements Iterator,Countable,ArrayAccess
{
	protected $propeties = array();
	protected $elements = array();

	public function add($propertyName, $element)
	{
		$this->elements[] = $element;

		if(isset($this->propeties[$propertyName]))
			$this->propeties[$propertyName][] = $element;
		else
			$this->propeties[$propertyName] = array($element);
		return $this;
	}

	public function get($propertyName)
	{
		if(isset($this->propeties[$propertyName]))
			return $this->propeties[$propertyName];
		else
			return null;
	}

 	public function offsetExists($propertyName)
 	{
 		return isset($this->propeties[$propertyName]);
 	}

 	public function offsetGet($propertyName)
 	{
 		return $this->get($propertyName);
 	}

 	public function offsetSet($propertyName, $value)
 	{
 		$this->add($propertyName, $value);
 	}

 	public function offsetUnset($propertyName)
 	{
        unset($this->propeties[$propertyName]);
        $this->elements = array();
        foreach($this->propeties as $list) {
        	foreach($list as $element) {
        		$this->elements[] = $element;
        	}
        }
 	}

	public function toArray()
	{
		return $this->elements;
	}

	public function isEmpty()
	{
		return empty($this->elements);
	}

	public function count()
	{
		return count($this->elements);
	}

	public function current()
	{
		return current($this->elements);
	}

	public function key()
	{
		return key($this->elements);
	}

	public function next()
	{
		next($this->elements);
	}

	public function rewind()
	{
		reset($this->elements);
	}

	public function valid()
	{
		return (key($this->elements) !== null);
	}
}