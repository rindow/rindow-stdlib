<?php
namespace Rindow\Stdlib\Cache;

class Apc
{
    public function clear_cache($cacheType)
    {
        return apc_clear_cache($cacheType);
    }

    public function exists($key)
    {
        return apc_exists($key);
    }

    public function fetch($key,&$success=null)
    {
        return apc_fetch($key,$success);
    }

    public function store($key, $value, $timeOut)
    {
        return apc_store($key, $value, $timeOut);
    }

    public function add($key, $value, $timeOut)
    {
        return apc_add($key, $value, $timeOut);
    }

    public function delete($key)
    {
        return apc_delete($key);
    }
}
