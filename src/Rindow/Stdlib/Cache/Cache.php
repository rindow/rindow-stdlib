<?php
namespace Rindow\Stdlib\Cache;

use Psr\SimpleCache\CacheInterface;

interface Cache extends CacheInterface
{
    public function isReady();
}