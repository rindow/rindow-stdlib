<?php
namespace Rindow\Stdlib\Cache;

use ArrayAccess;
use ArrayObject;

class CacheChain implements ArrayAccess
{
    const NOT_FOUND = '$$$$$$$$$$$$$NOTFOUND$$$$$$$$$$$$';
    protected $cache;
    protected $storage;

    public function __construct(ArrayAccess $storage=null,ArrayAccess $cache=null)
    {
        $this->storage = $storage;
        if($cache==null) 
            $this->cache = new ArrayCache();
        else
            $this->cache = $cache;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setTimeOut($timeOut)
    {
        $this->cache->setTimeOut($timeOut);
        if($this->storage)
            $this->storage->setTimeOut($timeOut);
        return $this;
    }

    public function offsetExists($offset)
    {
        if(!is_scalar($offset)) {
            throw new Exception\DomainException('Illegal offset type "'.gettype($offset).'".');
            //trigger_error('Illegal offset type "'.gettype($offset).'".',E_USER_NOTICE);
            return false;
        }
        $a = $this->cache->offsetExists($offset);
        if($a) {
            $entry = $this->cache->offsetGet($offset);
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$offset.'"');
            list($status,$value) = $entry;
            return $status;
        }
        if($this->storage) {
            if($this->storage->offsetExists($offset)) {
                $value = $this->storage->offsetGet($offset);
                $status = true;
            } else {
                $value = null;
                $status = false;
            }
            $entry = array($status,$value,true);
            $this->cache->offsetSet($offset,$entry);
        } else {
            $value = null;
            $status = false;
        }
        return $status;
    }

    public function offsetGet($offset)
    {
        if(!is_scalar($offset)) {
            throw new Exception\DomainException('Illegal offset type "'.gettype($offset).'".');
            //trigger_error('Illegal offset type "'.gettype($offset).'".',E_USER_NOTICE);
            return false;
        }
        if($this->cache->offsetExists($offset)) {
            $entry = $this->cache->offsetGet($offset);
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$offset.'"');
            list($status,$value) = $entry;
            if(!$status)
                trigger_error('Offset invalid or out of range.',E_USER_NOTICE);
            return $value;
        }
        if($this->storage) {
            if($this->storage->offsetExists($offset)) {
                $value = $this->storage->offsetGet($offset);
                $status = true;
            } else {
                $value = null;
                $status = false;
                $entry = array($status,$value);
                $this->cache->offsetSet($offset,$entry);
            }
        } else {
            $value = null;
            $status = false;
        }

        //throw new Exception\OutOfRangeException('Offset invalid or out of range.'); 
        if(!$status)
            trigger_error('Offset invalid or out of range.',E_USER_NOTICE);
        return $value;
    }

    public function offsetSet($offset, $value)
    {
        $entry = array(true,$value);
        $this->cache->offsetSet($offset, $entry);
        if($this->storage)
            $this->storage->offsetSet($offset, $value);
        return $this;
    }

    public function offsetUnset($offset)
    {
        if($this->cache->offsetExists($offset))
            $this->cache->offsetUnset($offset);
        if($this->storage)
            if($this->storage->offsetExists($offset))
               $this->storage->offsetUnset($offset);
        return $this;
    }

    public function containsKey($offset)
    {
        return $this->offsetExists($offset);
    }

    public function get($offset,$default=null,$callback=null)
    {
        if(!is_scalar($offset)) {
            throw new Exception\DomainException('Illegal offset type "'.gettype($offset).'".');
            //trigger_error('Illegal offset type "'.gettype($offset).'".',E_USER_NOTICE);
            return false;
        }

        if($this->storage==null) {
            if($callback==null) {
                $entry = $this->cache->get($offset,null);
                if($entry) {
                    if(!is_array($entry))
                        throw new Exception\DomainException('Invalid cache entity in "'.$offset.'"');
                    list($status,$value) = $entry;
                    if($status)
                        return $value;
                    else
                        return $default;
                } else {
                    $entry = array(false,null);
                    $this->cache->put($offset,$entry);
                    return $default;
                }
            }
            $entry = $this->cache->get($offset,$default,function($cache,$offset,&$entry) use ($callback) {
                $args = array($cache,$offset,&$value);
                if(call_user_func_array($callback,$args)) {
                    $entry = array(true,$value);
                    return true;
                } else {
                    $entry = array(false,null);
                    return true;
                }
            });
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$offset.'"');
            list($status,$value) = $entry;
            if($status)
                return $value;
            else
                return $default;
        }

        $entry = $this->cache->get($offset,null,null);
        if($entry!==null && is_array($entry)) {
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$offset.'"');
            list($status,$value) = $entry;
            if($status) {
                return $value;
            }
            if($callback===null) {
                return $default;
            }
        }
        $value = $this->storage->get($offset,self::NOT_FOUND,$callback);
        if($value===self::NOT_FOUND) {
            $entry = array(false,null);
        } else {
            $entry = array(true,$value);
        }
        $this->cache->put($offset,$entry);
        if($value===self::NOT_FOUND) {
            return $default;
        } else {
            return $value;
        }
    }

    public function put($offset, $value, $addMode=null)
    {
        $entry = array(true,$value);
        $this->cache->put($offset, $entry, $addMode);
        if($this->storage)
            $this->storage->put($offset, $value, $addMode);
        return $this;
    }

    public function remove($offset)
    {
        $this->offsetUnset($offset);
        return $this;
    }

    public function hasFileStorage()
    {
        if(method_exists($this->storage, 'hasFileStorage') && $this->storage->hasFileStorage())
            return true;
        if(method_exists($this->cache, 'hasFileStorage') && $this->cache->hasFileStorage())
            return true;
        return false;
    }
}