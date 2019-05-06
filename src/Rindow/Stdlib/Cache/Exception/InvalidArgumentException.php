<?php
namespace Rindow\Stdlib\Cache\Exception;

use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionInterface;

class InvalidArgumentException
extends \InvalidArgumentException 
implements InvalidArgumentExceptionInterface,ExceptionInterface
{}
