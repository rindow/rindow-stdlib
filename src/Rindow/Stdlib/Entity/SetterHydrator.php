<?php
namespace Rindow\Stdlib\Entity;

class SetterHydrator implements Hydrator
{
    public function hydrate(array $data, $object)
    {
        foreach ($data as $key => $value) {
            if(property_exists($object, $key)) {
                $setter = 'set'.ucfirst($key);
                if(is_callable(array($object,$setter)))
                    $object->$setter($value);
            }
        }
        return $object;
    }

    public function extract($object,array $keys=null)
    {
        if($keys==null) {
            throw new Exception\InvalidArgumentException('need keys to extract for "SetterHydrator"');
        }
        $result = array();
        foreach ($keys as $key) {
            if(property_exists($object, $key)) {
                $getter = 'get'.ucfirst($key);
                if(is_callable(array($object,$getter)))
                    $result[$key] = $object->$getter();
            }
        }
        return $result;
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