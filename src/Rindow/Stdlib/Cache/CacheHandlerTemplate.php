<?php
namespace Rindow\Stdlib\Cache;

class CacheHandlerTemplate
{
    protected $cacheInstances = array();
    protected $enableCache = true;
    protected $cachePath;
/*
    public static function instanceFactory($className,$cachePath=null)
    {
        if($cachePath)
            $path = rtrim($cachePath.'/\\');
        else
            $path = '/'.$className;

        $cache = CacheFactory::getMemoryInstance($path.'/instance');
        if(!isset($cache['instance']))
            $cache['instance'] = new $className();
        return $cache['instance'];
    }

    public static function hasInstance($className,$cachePath=null)
    {
        if($cachePath)
            $path = rtrim($cachePath.'/\\');
        else
            $path = '/'.$className;
        return CacheFactory::hasMemoryInstance($path.'/instance');
    }
*/
    public function __construct($cachePath=null) {
        if($cachePath)
            $this->setCachePath($cachePath);
    }

    public function setEnableCache($enable=true)
    {
        $this->enableCache = $enable;
    }

    public function getEnableCache()
    {
        return $this->enableCache;
    }

    public function setCachePath($cachePath)
    {
        $this->cachePath = rtrim($cachePath.'/\\');
    }

    public function getCache($name,$forceFileCache=null,$disableFileCache=null)
    {
        if(isset($this->cacheInstances[$name]))
            return $this->cacheInstances[$name];

        if(!$this->enableCache) {
            return $this->cacheInstances[$name] = new CacheChain();
        }
        if($this->cachePath)
            $path = $this->cachePath;
        else
            $path = '';

        return $this->cacheInstances[$name] = CacheFactory::getInstance($path.'/'.$name,$forceFileCache=null,$disableFileCache=null);
    }
}