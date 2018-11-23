<?php
namespace Rindow\Stdlib\Cache;

use ArrayAccess;

class ApcCache implements ArrayAccess
{
    protected $cachePath;
    protected $timeOut = 360; // 360 seconds = 10 minute.

    public static function isReady()
    {
        return extension_loaded('apc');
    }

    public static function clear($cacheType)
    {
        if($cacheType===null)
            $cacheType = 'user';
        apc_clear_cache($cacheType);
    }

    public function __construct($cachePath=null, $timeOut=null)
    {
    	if(!extension_loaded('apc'))
    		throw new Exception\DomainException('apc extension is not loaded.');
        if($cachePath!==null)
	        $this->setCachePath($cachePath);
        if($timeOut!==null)
	        $this->setTimeOut($timeOut);
    }
    
    public function setCachePath($cachePath)
    {
        $this->cachePath = $cachePath;
        return $this;
    }
    
    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    public function offsetExists($offset)
    {
        $key = $this->cachePath . '/' . $offset;
        return apc_exists($key);
    }

    public function offsetGet($offset)
    {
        $key = $this->cachePath . '/' . $offset;
        return apc_fetch($key);
    }

    public function offsetSet($offset,$value)
    {
        $key = $this->cachePath . '/' . $offset;
        apc_store($key, $value, $this->timeOut);
        return $this;
    }

    public function offsetUnset($offset)
    {
        $key = $this->cachePath . '/' . $offset;
		apc_delete($key);
        return $this;
    }

    public function containsKey($offset)
    {
        return $this->offsetExists($offset);
    }

    public function get($offset,$default=null,$callback=null)
    {
        $key = $this->cachePath . '/' . $offset;
        $success = false;
        $value = apc_fetch($key,$success);
        if($success)
            return $value;
        if($callback==null)
            return $default;
        $value = $default;
        $args = array($this, $offset, &$value);
        if(call_user_func_array($callback,$args)) {
            apc_store($key, $value, $this->timeOut);
        }
        return $value;
    }

    public function put($offset, $value, $addMode=null)
    {
        $key = $this->cachePath . '/' . $offset;
        if($addMode) {
            apc_add($key, $value, $this->timeOut);
            return $this;
        }
        apc_store($key, $value, $this->timeOut);
        return $this;
    }

    public function remove($offset)
    {
        $this->offsetUnset($offset);
        return $this;
    }

    public function hasFileStorage()
    {
        return false;
    }
}