<?php
namespace Rindow\Stdlib\Cache\SimpleCache;

use Rindow\Stdlib\Cache\Cache;
use Rindow\Stdlib\Cache\Exception\InvalidArgumentException;
use Rindow\Stdlib\Cache\Exception\CacheException;
use Traversable;
use Memcache;

class MemcacheCache implements Cache
{
    protected $memcache;
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

    protected function loadMemcache()
    {
        if($this->memcache)
            return $this->memcache;

        if(!extension_loaded('memcache'))
            throw new CacheException('memcache extension is not loaded.');
        $memcache = new Memcache;
        $first = true;
        if(isset($config['servers'])) {
            if(is_array($config['servers']))
                $servers = $config['servers'];
        } else {
            $servers = array('localhost'=>true);
        }
        foreach($servers as $server=>$switch) {
            if(!$switch)
                continue;
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
        $this->memcache = $memcache;
        return $memcache;
    }

    public function isReady()
    {
        return extension_loaded('memcache');
    }

    public function get($key,$default=null)
    {
        $this->loadMemcache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        $success = 0;
        $value = $this->memcache->get($key,$success);
        if(!$success)
            return $default;
        return $value;
    }

    public function set($key,$value,$ttl=null)
    {
        $this->loadMemcache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        return $this->memcache->set($key, $value, MEMCACHE_COMPRESSED|MEMCACHE_USER1, $ttl);
    }

    public function delete($key)
    {
        $this->loadMemcache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        return $this->memcache->delete($key);
    }

    public function clear()
    {
        $this->loadMemcache();
        return $this->memcache->flush();
    }

    public function getMultiple($keys, $default = null)
    {
        $this->loadMemcache();
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
        $this->loadMemcache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        $success = 0;
        $value = $this->memcache->get($key,$success);
        if(!$success)
            return false;
        return true;
    }

    public function isNonvolatile()
    {
        return false;
    }
}