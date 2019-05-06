<?php
namespace Rindow\Stdlib\Cache\ConfigCache;

use Rindow\Stdlib\Cache\Cache;
use Rindow\Stdlib\Cache\SimpleCache\ArrayCache;
use InvalidArgumentException;
use Traversable;

class ConfigCache implements Cache
{
    const NOT_FOUND = '$$$$$$$$$$$$$NOTFOUND$$$$$$$$$$$$';
    protected $path;
    protected $primary;
    protected $secondary;

    public function __construct($path=null, $primary=null, $secondary=null)
    {
        $this->path = $path;
        $this->primary = $primary;
        $this->secondary = $secondary;
        //if($primary==null) 
        //    $this->primary = new ArrayCache();
        //else
        //    $this->primary = $primary;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function getSecondary()
    {
        return $this->secondary;
    }

    protected function generateKey($key)
    {
        if(!is_scalar($key)) {
            throw new Exception\InvalidArgumentException('Illegal offset type "'.gettype($key).'".');
        }
        if($this->path===null)  // When it will be used as nested cache, 
            return $key;        // the path will be null.
        return $this->path.'/'.$key;
    }

    public function has($key)
    {
        $key = $this->generateKey($key);
        $a = $this->primary->has($key);
        if($a) {
            $entry = $this->primary->get($key);
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$key.'"');
            list($status,$value) = $entry;
            return $status;
        }
        if($this->secondary) {
            if($this->secondary->has($key)) {
                $value = $this->secondary->get($key);
                $status = true;
            } else {
                $value = null;
                $status = false;
            }
            $entry = array($status,$value);
            $this->primary->set($key,$entry);
        } else {
            $value = null;
            $status = false;
        }
        return $status;
    }

    public function get($key,$default=null)
    {
        $key = $this->generateKey($key);
        if($this->primary->has($key)) {
            $entry = $this->primary->get($key);
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$key.'"');
            list($status,$value) = $entry;
            if(!$status)
                return $default;
            return $value;
        }
        if($this->secondary) {
            if($this->secondary->has($key)) {
                $value = $this->secondary->get($key);
                $status = true;
            } else {
                $value = null;
                $status = false;
            }
            $entry = array($status,$value);
            $this->primary->set($key,$entry);
        } else {
            $value = null;
            $status = false;
        }

        if(!$status)
            return $default;
        return $value;
    }

    public function set($key, $value, $ttl=null)
    {
        $key = $this->generateKey($key);
        $entry = array(true,$value);
        $result = $this->primary->set($key, $entry, $ttl);
        if($this->secondary)
            $result = $this->secondary->set($key, $value, $ttl);
        return $result;
    }

    public function delete($key)
    {
        $key = $this->generateKey($key);
        if($this->primary->has($key))
            $result = $this->primary->delete($key);
        else
            $result = false;

        if($this->secondary) {
            if($this->secondary->has($key))
                $result = $this->secondary->delete($key);
            else
                $result = false;
        }
        return $result;
    }

    public function getEx($key,$callback,array $arguments=null,$ttl=null)
    {
        $key = $this->generateKey($key);
        if(!is_callable($callback)) {
            throw new Exception\InvalidArgumentException('Illegal callback type "'.gettype($cal).'".');
            //trigger_error('Illegal offset type "'.gettype($key).'".',E_USER_NOTICE);
            return false;
        }
        if($this->secondary==null) {
            return $this->getExWithoutStrage($key,$callback,$arguments,$ttl);
        } else {
            return $this->getExWithStrage($key,$callback,$arguments,$ttl);
        }
    }

    protected function getExWithoutStrage($key,$callback,array $arguments=null,$ttl=null)
    {
        $entry = $this->primary->get($key,self::NOT_FOUND);
        if($entry==self::NOT_FOUND) {
            $save = true;
            $args = array($key,$arguments,&$save);
            $value = call_user_func_array($callback,$args);
            $entry = array(true,$value);
            if($save)
                $this->primary->set($key,$entry,$ttl);
        }
        if(!is_array($entry))
            throw new Exception\DomainException('Invalid cache entity in "'.$key.'"');
        list($status,$value) = $entry;
        return $value;
    }

    protected function getExWithStrage($key,$callback,array $arguments=null,$ttl=null)
    {
        $entry = $this->primary->get($key,self::NOT_FOUND);
        if($entry!=self::NOT_FOUND) {
            if(!is_array($entry))
                throw new Exception\DomainException('Invalid cache entity in "'.$key.'"');
            list($status,$value) = $entry;
            if($status)
                return $value;
        } else {
            $value = $this->secondary->get($key,self::NOT_FOUND);
            if($value!=self::NOT_FOUND) {
                $status = true;
            } else {
                $status = false;
                $value = null;
            }
            $entry = array($status,$value);
        }
        $save = true;
        if(!$status) {
            $args = array($key,$arguments,&$save);
            $value = call_user_func_array($callback,$args);
            $entry = array(true,$value);
        }
        if($save) {
            $this->secondary->set($key,$value,$ttl);
            $this->primary->set($key,$entry,$ttl);
        }
        return $value;
    }

    public function isNonvolatile()
    {
        if($this->secondary && $this->secondary->isNonvolatile())
            return true;
        if($this->primary->isNonvolatile())
            return true;
        return false;
    }

    public function isReady()
    {
        if($this->primary->isReady()) {
            if($this->secondary==null)
                return true;
            if($this->secondary->isReady())
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    public function clear()
    {
        $this->primary->clear();
        if($this->secondary)
            $this->secondary->clear();
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
}
