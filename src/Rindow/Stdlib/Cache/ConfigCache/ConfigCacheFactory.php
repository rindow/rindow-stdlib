<?php
namespace Rindow\Stdlib\Cache\ConfigCache;

use Rindow\Stdlib\Cache\Exception;
use Rindow\Stdlib\Cache\SimpleCache\ArrayCache;

class ConfigCacheFactory
{
    const DEFAULT_MEMCACHE    = 'Rindow\\Stdlib\\Cache\\SimpleCache\\ApcCache';
    const DEFAULT_FILECACHE   = 'Rindow\\Stdlib\\Cache\\SimpleCache\\FileCache';
    const DEFAULT_CONFIGCACHE = 'Rindow\\Stdlib\\Cache\\ConfigCache\\ConfigCache';

    protected $memCache;
    protected $fileCache;
    protected $memCacheClassName = self::DEFAULT_MEMCACHE;
    protected $fileCacheClassName = self::DEFAULT_FILECACHE;
    protected $configCacheClassName = self::DEFAULT_CONFIGCACHE;
    protected $enableCache = true;
    protected $enableMemCache  = true;
    protected $enableFileCache = true;
    protected $forceFileCache  = false;
    protected $fakeCache;

    public function __construct(array $config=null)
    {
        if($config)
            $this->setConfig($config);
    }

    public function setConfig($config)
    {
        $this->config = $config;
        if(isset($config['memCache']['class']))
            $this->memCacheClassName = $config['memCache']['class'];
        if(isset($config['fileCache']['class']))
            $this->fileCacheClassName = $config['fileCache']['class'];
        if(isset($config['configCache'])) {
            $cacheConfig = $config['configCache'];
            if(isset($cacheConfig['class']))
                $this->configCacheClassName = $cacheConfig['class'];
            if(isset($cacheConfig['enableMemCache']))
                $this->enableMemCache = $cacheConfig['enableMemCache'];
            if(isset($cacheConfig['enableFileCache']))
                $this->enableFileCache = $cacheConfig['enableFileCache'];
            if(isset($cacheConfig['forceFileCache']))
                $this->forceFileCache = $cacheConfig['forceFileCache'];
            if(isset($cacheConfig['enableCache']))
                $this->enableCache = $cacheConfig['enableCache'];
        }
        if(isset($config['enableCache'])&&
            !isset($config['configCache']['enableCache'])) {
            $this->enableCache = $config['enableCache'];
        }
    }

    public function setMemCache($memCache)
    {
        $this->memCache = $memCache;
    }

    public function setFileCache($fileCache)
    {
        $this->fileCache = $fileCache;
    }

    public function setEnableCache($enableCache)
    {
        $this->enableCache = $enableCache;
    }

    public function getEnableCache()
    {
        return $this->enableCache;
    }

    public function create($path,$forceFileCache=null,$disableFileCache=null)
    {
        $className = $this->configCacheClassName;
        if(!class_exists($className))
            throw new Exception\DomainException('Config cache class not found: '.$className);
        if(!is_string($path))
            throw new Exception\InvalidArgumentException('path must be string.');
        $path = str_replace('\\', '/', $path);

        if(!$this->enableCache)
            return new $className($path,$this->getFakeCache());

        $memCache = $fileCache = null;
        if($this->enableMemCache) {
            $memCache = $this->getPrimaryCache();
        }

        if(!$disableFileCache) {
            if($this->forceFileCache || $forceFileCache|| ($this->enableFileCache && $memCache==null)) {
                $fileCache = $this->getSecondaryCache();
            }
        }
        if($memCache==null) {
            $memCache = $this->getFakeCache();
        }
        $configCache = new $className($path,$memCache,$fileCache);
        return $configCache;
    }

    protected function getPrimaryCache()
    {
        return $this->getMemCache();
    }

    protected function getSecondaryCache()
    {
        return $this->getFileCache();
    }

    public function getFakeCache()
    {
        if($this->fakeCache==null)
            $this->fakeCache = new ArrayCache();
        return $this->fakeCache;
    }

    public function getMemCache()
    {
        if(!$this->memCache)
            $this->memCache = $this->createMemoryCache($this->memCacheClassName);
        if(!$this->memCache->isReady())
            return null;
        return $this->memCache;
    }

    public function getFileCache()
    {
        if(!$this->fileCache)
            $this->fileCache = $this->createFileCache($this->fileCacheClassName);
        if(!$this->fileCache->isReady())
            return null;
        return $this->fileCache;
    }

    protected function createMemoryCache($className)
    {
        if(!class_exists($className))
            throw new Exception\DomainException('Memory cache class not found: '.$className);
        $config = null;
        if(isset($this->config['memCache']))
            $config = $this->config['memCache'];
        $memCache = new $className($config);
        return $memCache;
    }

    protected function createFileCache($className)
    {
        if(!class_exists($className))
            throw new Exception\DomainException('File cache class not found: '.$className);
        if(isset($this->config['fileCache']))
            $config = $this->config['fileCache'];
        else
            $config = array();
        if(!isset($config['path']) && isset($this->config['filePath']))
            $config['path'] = $this->config['filePath'];
        if(empty($config))
            $config = null;
        $fileCache = new $className($config);
        return $fileCache;
    }
}
