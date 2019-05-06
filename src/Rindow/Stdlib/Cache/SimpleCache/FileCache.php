<?php
namespace Rindow\Stdlib\Cache\SimpleCache;

use Rindow\Stdlib\Cache\Cache;
use Rindow\Stdlib\Cache\Exception\InvalidArgumentException;
use Rindow\Stdlib\Cache\Exception\CacheException;
use Traversable;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FileCache implements Cache
{
    const NOT_FOUND = '$$$$$$$$$$$$$NOTFOUND$$$$$$$$$$$$';
    protected $config;
    protected $cachePath;
    protected $lastKey;
    protected $hasLastValue = false;
    protected $lastGetTime;
    protected $lastTimeout;
    protected $lastValue;
    protected $fastGetTtl = 0;

    public function __construct($config=null)
    {
        if($config)
            $this->setConfig($config);
    }

    public function setConfig($config)
    {
        $this->config = $config;
        if(isset($config['path']))
            $this->cachePath = $config['path'];
    }

    public function getPath()
    {
        $this->loadFilecache();
        return $this->cachePath;
    }

    protected function loadFilecache()
    {
        if($this->cachePath==null)
            $this->cachePath = $this->getDefaultFileCachePath();
    }

    public function getDefaultFileCachePath()
    {
        return sys_get_temp_dir().'/cache';
    }

    public function transFromOffsetToPath($offset)
    {
        return str_replace(
            array('\\',':',  '*',  '?',  '"',  '<',  '>',  '|',  '.'),
            array('/', '%3A','%2A','%3F','%22','%3C','%3E','%7C','%46'),
            $offset);
    }

    public function isReady()
    {
        return true;
    }

    public function setFastGetTtl($fastGetTtl)
    {
        $this->fastGetTtl = $fastGetTtl;
    }

    protected function clearLastGetItem()
    {
        $this->lastKey = null;
        $this->hasLastValue = false;
    }

    public function get($key,$default=null)
    {
        $this->loadFilecache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        $now = time();
        if($this->lastKey===$key && $this->hasLastValue) {
            if($this->lastGetTime && $now<=$this->lastGetTime+$this->fastGetTtl) {
                if(!$this->lastTimeout || $now<$this->lastTimeout) {
                    return $this->lastValue;
                }
            }
        }
        $this->clearLastGetItem();

        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($key) . '.php';
        if(!file_exists($filename)) {
            return $default;
        }
        $item = require $filename;
        if(!is_array($item))
            throw new DomainException('Invalid cache item format.');
        list($timeout,$value) = $item;
        $this->lastTimeout = $timeout;
        if($timeout && $now>=$timeout) {
            return $default;
        }
        $this->lastValue = $value;
        $this->lastTimeout = $timeout;
        $this->lastKey = $key;
        $this->hasLastValue = true;
        $this->lastGetTime = $now;
        return $value;
    }

    public function set($key,$value,$ttl=null)
    {
        $this->loadFilecache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        $this->clearLastGetItem();

        if($ttl)
            $timeout = time() + $ttl;
        else
            $timeout = null;
        $code = "<?php\nreturn unserialize('".str_replace(array('\\','\''), array('\\\\','\\\''), serialize(array($timeout,$value)))."');";
        //$code = "<?php\nreturn unserialize(\"".str_replace(array("\\","\0","\"","\n","\r","\t"), array("\\\\","\\0","\\\"","\\n","\\r","\\t"), serialize($value))."\");";
        //$code = "<?php\nreturn unserialize(base64_decode('".base64_encode(serialize($value))."'));";
        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($key) . '.php';
        if(!is_dir(dirname($filename))) {
            $dirname = dirname($filename);
            $stat = mkdir(dirname($filename),0777,true);
            if($stat===false)
                return false;
        }
        $stat = file_put_contents($filename, $code);
        if($stat===false)
            return false;
        return true;
    }

    public function delete($key)
    {
        $this->loadFilecache();
        if(!is_string($key) && !is_numeric($key))
            throw new InvalidArgumentException('Key must be string.');
        $this->clearLastGetItem();

        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($key) . '.php';
        if(!file_exists($filename))
            return false;
        $stat = unlink($filename);
        if($stat===false)
            return false;
        return true;
    }

    public function clear()
    {
        $this->loadFilecache();
        if(!file_exists($this->cachePath)) {
            return;
        }
        $this->clearLastGetItem();
        $fileSPLObjects = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->cachePath),
                RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($fileSPLObjects as $fullFileName => $fileSPLObject) {
            $filename = $fileSPLObject->getFilename();
            if (is_dir($fullFileName)) {
                if($filename!='.' && $filename!='..') {
                    @rmdir($fullFileName);
                }
            } else {
                @unlink($fullFileName);
            }
        }
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
        $value = $this->get($key,self::NOT_FOUND);
        if($value===self::NOT_FOUND)
            return false;
        return true;
    }

    public function isNonvolatile()
    {
        return true;
    }
}