<?php
namespace Rindow\Stdlib\Cache\SimpleCache;

use Rindow\Stdlib\Cache\Cache;
use Rindow\Stdlib\Cache\Exception\InvalidArgumentException;
use Rindow\Stdlib\Cache\Exception\CacheException;
use Rindow\Stdlib\Cache\Support\Apc;
use Rindow\Stdlib\Cache\Support\Apcu;
use Traversable;

class ApcCache implements Cache
{
    protected $apc;
    protected $config;

    public function __construct($config=null)
    {
        if(PHP_SAPI=='cli'&&!ini_get('apc.enable_cli'))
            $this->apc = null;
        elseif(extension_loaded('apcu'))
            $this->apc = new Apcu();
        elseif(extension_loaded('apc'))
            $this->apc = new Apc();

        $this->setConfig($config);
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function isReady()
    {
        return ($this->apc!=null);
    }

    protected function assertApcLoaded()
    {
        if($this->apc==null)
            throw new CacheException('apc or apcu extension is not loaded. Or apc.cli_enable is 0');
    }

    public function get($key,$default=null)
    {
        $this->assertApcLoaded();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        $success = false;
        $value = $this->apc->fetch($key,$success);
        if(!$success)
            return $default;
        return $value;
    }

    public function set($key,$value,$ttl=null)
    {
        $this->assertApcLoaded();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        return $this->apc->store($key, $value, $ttl);
    }

    public function delete($key)
    {
        $this->assertApcLoaded();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');

        return $this->apc->delete($key);
    }

    public function clear()
    {
        $this->assertApcLoaded();
        return $this->apc->clear('user');
    }

    public function getMultiple($keys, $default = null)
    {
        if(!is_array($keys) && !($keys instanceof Traversable))
            throw new InvalidArgumentException('Keys must be array or Traversable.');

        $values = array();
        foreach ($keys as $key) {
            $values[$key] = $this->get($key,$default);
        }
        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->assertApcLoaded();
        if(!is_array($values) && !($values instanceof Traversable))
            throw new InvalidArgumentException('Values must be array or Traversable.');
        if($values instanceof Traversable) {
            $newValues = array();
            foreach ($values as $key => $value) {
                if(!is_string($key) && !is_numeric($key))
                    throw new InvalidArgumentException('Key must be string.');
                $newValues[$key] = $value;
            }
            $values = $newValues;
        }
        $errors = $this->apc->store($values,null,$ttl);
        if(empty($errors))
            return true;
        else
            return false;
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
        $this->assertApcLoaded();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        return $this->apc->exists($key);
    }

    public function isNonvolatile()
    {
        return false;
    }
}
