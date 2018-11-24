<?php
namespace Rindow\Stdlib\Cache;

use ArrayAccess;

class ApcCache implements ArrayAccess
{
    protected $cachePath;
    protected $timeOut = 360; // 360 seconds = 10 minute.
    protected $apc;

    public static function isReady()
    {
        return extension_loaded('apcu') || extension_loaded('apc');
    }

    public static function clear($cacheType)
    {
        if($cacheType===null)
            $cacheType = 'user';
        if(extension_loaded('apcu'))
            apcu_clear_cache();
        elseif(extension_loaded('apc'))
            apc_clear_cache($cacheType);
        else
            throw new Exception\DomainException('apc or apcu extension is not loaded.');
    }

    public function __construct($cachePath=null, $timeOut=null)
    {
        if(extension_loaded('apcu'))
            $this->apc = new Apcu();
        elseif(extension_loaded('apc'))
            $this->apc = new Apc();
        else
            throw new Exception\DomainException('apc or apcu extension is not loaded.');
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
        return $this->apc->exists($key);
    }

    public function offsetGet($offset)
    {
        $key = $this->cachePath . '/' . $offset;
        return $this->apc->fetch($key);
    }

    public function offsetSet($offset,$value)
    {
        $key = $this->cachePath . '/' . $offset;
        $this->apc->store($key, $value, $this->timeOut);
        return $this;
    }

    public function offsetUnset($offset)
    {
        $key = $this->cachePath . '/' . $offset;
        $this->apc->delete($key);
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
        $value = $this->apc->fetch($key,$success);
        if($success)
            return $value;
        if($callback==null)
            return $default;
        $value = $default;
        $args = array($this, $offset, &$value);
        if(call_user_func_array($callback,$args)) {
            $this->apc->store($key, $value, $this->timeOut);
        }
        return $value;
    }

    public function put($offset, $value, $addMode=null)
    {
        $key = $this->cachePath . '/' . $offset;
        if($addMode) {
            $this->apc->add($key, $value, $this->timeOut);
            return $this;
        }
        $this->apc->store($key, $value, $this->timeOut);
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