<?php
namespace Rindow\Stdlib\Cache\Exception;

use Psr\SimpleCache\CacheException as CacheExceptionInterface;

class CacheException extends \DomainException
implements CacheExceptionInterface,ExceptionInterface
{}
