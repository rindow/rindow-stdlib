<?php
namespace RindowTest\StdCollection\IteratorIteratorAggregateTest;

use Rindow\Stdlib\IteratorIteratorAggregate;

class TestModifyer extends IteratorIteratorAggregate
{
    protected $_key = 100;

    protected function current()
    {
        $value = parent::current();
        $value = 'Hello '.$value;
        return $value;
    }

    protected function key()
    {
        $key = parent::key();
        $key = 'Key('.$this->_key.') '.$key;
        return $key;
    }

    protected function rewind()
    {
        parent::rewind();
        $this->_key = 1000;
    }

    protected function next()
    {
        $this->_key += 1;
    }
}

class TestStatmentIterator extends IteratorIteratorAggregate
{
    protected function current()
    {
        $row = parent::current();
        $row['modified'] = 'Hello';
        return $row;
    }
}
