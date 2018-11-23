<?php
namespace Rindow\Stdlib\Entity;

class EntityHydrator implements Hydrator
{
    public function hydrate(array $data,  $object)
    {
    	if(!($object instanceof Entity))
    		throw new Exception\InvalidArgumentException('a object must be a instance of "Entity"');
        return $object->hydrate($data);
    }

    public function extract($object,array $keys=null)
    {
    	if(!($object instanceof Entity))
    		throw new Exception\InvalidArgumentException('a object must be a instance of "Entity"');
        return $object->extract($keys);
    }

    public function set($object,$name,$value)
    {
        $setter = 'set'.ucfirst($name);
        if(!is_callable(array($object,$getter))) {
            if(is_object($object))
                $target = get_class($object);
            else
                $target = gettype($object);
            throw new Exception\DomainException('setter is not exist for field "'.$name.'" on '.$target);
        }
        $object->$setter($value);
        return $this;
    }

    public function get($object,$name)
    {
        $getter = 'get'.ucfirst($name);
        if(!is_callable(array($object,$getter))) {
            if(is_object($object))
                $target = get_class($object);
            else
                $target = gettype($object);
            throw new Exception\DomainException('getter is not exist for field "'.$name.'" on '.$target);
        }
        return $object->$getter();
    }
}