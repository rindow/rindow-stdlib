<?php
namespace Rindow\Stdlib\Entity;

interface Hydrator
{
    public function hydrate(array $data, $object);
    public function extract($object, array $keys=null);
    public function set($object,$name,$value);
    public function get($object,$name);
}
