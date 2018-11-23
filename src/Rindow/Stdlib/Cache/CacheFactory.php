<?php
namespace Rindow\Stdlib\Cache;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class CacheFactory
{
    const DEFAULT_MEMCACHE  = 'Rindow\Stdlib\Cache\ApcCache';
    const DEFAULT_FILECACHE = 'Rindow\Stdlib\Cache\FileCache';

    public static $enableMemCache  = true;
    public static $enableFileCache = true;
    public static $forceFileCache  = false;
    public static $notRegister = false;
    public static $fileCachePath;
    public static $memCacheTimeOut;
    public static $caches = array();
    public static $memCacheClassName = self::DEFAULT_MEMCACHE;
    public static $fileCacheClassName = self::DEFAULT_FILECACHE;

    public static function newInstance($path,$forceFileCache=null,$disableFileCache=null)
    {
        $memCacheClassName = self::$memCacheClassName;
        if($memCacheClassName::isReady() && self::$enableMemCache) {
            $mem = new $memCacheClassName($path,self::$memCacheTimeOut);
        }

        if(!$disableFileCache) {
            $fileCacheClassName = self::$fileCacheClassName;
            if(self::$forceFileCache || $forceFileCache|| (!isset($mem) && self::$enableFileCache)) {
                $fileCachePath = (self::$fileCachePath) ? self::$fileCachePath : sys_get_temp_dir();
                $path = rtrim(str_replace('\\', '/', $fileCachePath)).'/'.trim(str_replace('\\', '/', $path),'/');
                $file = new $fileCacheClassName($path);
            }
        }

        if(isset($file) && isset($mem)) {
            $secondary = new CacheChain($file,$mem);
            $primary = new CacheChain($secondary);
        }
        else if(isset($mem)) {
            $primary = new CacheChain($mem);
        }
        else if(isset($file)) {
            $primary = new CacheChain($file);
        }
        else {
            $primary = new ArrayCache();
        }

        return $primary;
    }

    public static function getInstance($path,$forceFileCache=null,$disableFileCache=null)
    {
        if(self::$notRegister)
            return new ArrayCache();
        if(!isset(self::$caches[$path]))
            self::$caches[$path] = self::newInstance($path,$forceFileCache,$disableFileCache);

        return self::$caches[$path];
    }

    public static function getMemoryInstance($path)
    {
        if(self::$notRegister)
            return new ArrayCache();
        if(!isset(self::$caches[$path]))
            self::$caches[$path] = new ArrayCache();

        return self::$caches[$path];
    }

    public static function hasMemoryInstance($path)
    {
        if(self::$notRegister)
            return false;
        return isset(self::$caches[$path]);
    }

    public static function clearCache()
    {
        self::clearFileCache();
        self::clearMemCache();
        self::$caches = array();
    }

    public static function clearFileCache($path=null)
    {
        $fileCacheClassName = self::$fileCacheClassName;
        if($path===null) {
            $fileCachePath = (self::$fileCachePath) ? self::$fileCachePath : $fileCacheClassName::getDefaultFileCachePath();
            $path = rtrim(str_replace('\\', '/', $fileCachePath),'/');
        }
        $fileCacheClassName::clear($path);
    }

    public static function clearMemCache($cacheType=null)
    {
        $memCacheClassName = self::$memCacheClassName;
        if(!$memCacheClassName::isReady())
            return;
        $memCacheClassName::clear($cacheType);
    }

    public static function setConfig($config=null)
    {
        if(!is_array($config))
            return;
        if(array_key_exists('enableMemCache', $config))
            self::$enableMemCache = $config['enableMemCache'];
        if(array_key_exists('enableFileCache', $config))
            self::$enableFileCache = $config['enableFileCache'];
        if(array_key_exists('forceFileCache', $config))
            self::$forceFileCache = $config['forceFileCache'];
        if(array_key_exists('fileCachePath', $config))
            self::$fileCachePath = $config['fileCachePath'];
        if(array_key_exists('memCacheTimeOut', $config))
            self::$memCacheTimeOut = $config['memCacheTimeOut'];
        if(array_key_exists('memCacheClassName', $config))
            self::$memCacheClassName = $config['memCacheClassName'];
        if(array_key_exists('fileCacheClassName', $config))
            self::$fileCacheClassName = $config['fileCacheClassName'];
    }
}