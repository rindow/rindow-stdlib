<?php
namespace Rindow\Stdlib\Cache;

class Apcu
{
    public function clear_cache($cacheType)
    {
        return apcu_clear_cache();
    }

    public function exists($key)
    {
        return apcu_exists($key);
    }

    public function fetch($key,&$success=null)
    {
        $value = apcu_fetch($key,$success);
        return $value;
    }

    public function store($key, $value, $timeOut)
    {
        return apcu_store($key, $value, $timeOut);
    }

    public function add($key, $value, $timeOut)
    {
        return apcu_add($key, $value, $timeOut);
    }

    public function delete($key)
    {
        return apcu_delete($key);
    }
}
