<?php
namespace Rindow\Stdlib\Cache\SimpleCache;

use Rindow\Stdlib\Cache\Cache;
use Rindow\Stdlib\Cache\Exception\InvalidArgumentException;
use Rindow\Stdlib\Cache\Exception\CacheException;
use Traversable;

class ArrayCache implements Cache
{
    protected $cache = array();
    protected $config;

    public function __construct($config=null)
    {
        if($config)
            $this->setConfig($config);
    }

    public function setConfig($config)
    {
        if($config)
            $this->config = $config;
    }

    public function loadArrayCache()
    {
    }

    public function isReady()
    {
        return true;
    }

    public function get($key,$default=null)
    {
        $this->loadArrayCache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        if(!array_key_exists($key, $this->cache))
            return $default;
        return $this->cache[$key];
    }

    public function set($key,$value,$ttl=null)
    {
        $this->loadArrayCache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        $this->cache[$key] = $value;
        return true;
    }

    public function delete($key)
    {
        $this->loadArrayCache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        if(!array_key_exists($key, $this->cache))
            return false;
        unset($this->cache[$key]);
        return true;
    }

    public function clear()
    {
        $this->loadArrayCache();
        $this->cache = array();
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        if(!is_array($keys) && !($keys instanceof Traversable))
            throw new InvalidArgumentException('Keys must be array or Traversable.');

        foreach ($keys as $key) {
            $values[$key] = $this->get($key,$default);
        }
        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        if(!is_array($values) && !($values instanceof Traversable))
            throw new InvalidArgumentException('Values must be array or Traversable.');
        $success = true;
        foreach ($values as $key => $value) {
            if(!$this->set($key,$value,$ttl))
                $success = false;
        }
        return $success;
    }

    public function deleteMultiple($keys)
    {
        if(!is_array($keys) && !($keys instanceof Traversable))
            throw new InvalidArgumentException('Keys must be array or Traversable.');
        $success = true;
        foreach ($keys as $key) {
            if(!$this->delete($key))
                $success = false;
        }
        return $success;
    }

    public function has($key)
    {
        $this->loadArrayCache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        return array_key_exists($key,$this->cache);
    }

    public function isNonvolatile()
    {
        return false;
    }

    public function getAllKeys()
    {
        return array_keys($this->cache);
    }
}