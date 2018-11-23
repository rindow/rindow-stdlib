<?php
namespace Rindow\Stdlib\Cache;

use ArrayObject;

class ArrayCache extends ArrayObject
{
    public function setTimeOut($timeOut)
    {
        return $this;
    }

    public function containsKey($offset)
    {
        return $this->offsetExists($offset);
    }

    public function get($offset,$default=null,$callback=null)
    {
        if($this->offsetExists($offset)) {
            return $this->offsetGet($offset);
        }
        if($callback==null)
            return $default;
        $value = $default;
        $args = array($this, $offset, &$value);
        if(call_user_func_array($callback,$args)) {
            $this->put($offset,$value);
        }
        return $value;
    }

    public function put($offset, $value, $addMode=null)
    {
        if($addMode) {
            if($this->offsetExists($offset))
                return $this;
        }
        $this->offsetSet($offset,$value);
        return $this;
    }

    public function remove($offset)
    {
        $this->offsetUnset($offset);
        return $this;
    }
}
