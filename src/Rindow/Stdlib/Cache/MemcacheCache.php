<?php
namespace Rindow\Stdlib\Cache;

use ArrayAccess;

class MemcacheCache implements ArrayAccess
{
    public static $servers = array('localhost');
    protected $cachePath;
    protected $timeOut = 360; // 360 seconds = 10 minute.
    protected $memcache;

    public static function isReady()
    {
        return extension_loaded('memcache');
    }

    public static function factoryMemcache()
    {
        if(!extension_loaded('memcache'))
            throw new Exception\DomainException('memcache extension is not loaded.');
        $initialized = CacheHandlerTemplate::hasInstance('Memcache');
        $memcache = CacheHandlerTemplate::instanceFactory('Memcache');
        if($initialized)
            return $memcache;
        $first = true;
        foreach(self::$servers as $server) {
            $server = explode(':',$server);
            if(!isset($server[1]))
                $server[1] = 11211;
            list($host,$port) = $server;
            if($first) {
                $memcache->connect($host,$port);
                $first = false;
            } else {
                $memcache->addServer($host,$port);
            }
        }
        return $memcache;
    }

    public static function clear($cacheType)
    {
        $memcache = self::factoryMemcache();
        $memcache->flush();
    }

    public function __construct($cachePath=null, $timeOut=null)
    {
        $this->memcache = self::factoryMemcache();
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
        $result = $this->memcache->get($key);
        if($result!==false)
            return true;
        else
            return false;
    }

    public function offsetGet($offset)
    {
        $key = $this->cachePath . '/' . $offset;
        return $this->memcache->get($key);
    }

    public function offsetSet($offset,$value)
    {
        $key = $this->cachePath . '/' . $offset;
        $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $this->timeOut);
        return $this;
    }

    public function offsetUnset($offset)
    {
        $key = $this->cachePath . '/' . $offset;
        $this->memcache->delete($key);
        return $this;
    }

    public function containsKey($offset)
    {
        return $this->offsetExists($offset);
    }

    public function get($offset,$default=null,$callback=null)
    {
        $key = $this->cachePath . '/' . $offset;
        $value = $this->memcache->get($key);
        if($value!==false)
            return $value;
        if($callback==null)
            return $default;
        $value = $default;
        $args = array($this, $offset, &$value);
        if(call_user_func_array($callback,$args)) {
            $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $this->timeOut);
        }
        return $value;
    }

    public function put($offset, $value, $addMode=null)
    {
        $key = $this->cachePath . '/' . $offset;
        if($addMode) {
            $this->memcache->add($key, $value, MEMCACHE_COMPRESSED, $this->timeOut);
            return $this;
        }
        $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $this->timeOut);
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