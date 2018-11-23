<?php
namespace Rindow\Stdlib\Entity;

use ReflectionClass;

class ReflectionHydrator implements Hydrator
{
    protected static $reflections = array();

    public function hydrate(array $data, $object)
    {
        $reflections = self::getReflections(get_class($object));
        foreach ($data as $key => $value) {
            if(isset($reflections[$key])) {
                $reflections[$key]->setValue($object,$value);
            }
        }
        return $object;
    }

    public function extract($object,array $keys=null)
    {
        $reflections = self::getReflections(get_class($object));
        $result = array();
        foreach ($reflections as $name => $ref) {
            $result[$name] = $ref->getValue($object);
        }
        return $result;
    }

    public static function getReflections($className)
    {
        if(isset(self::$reflections[$className]))
            return self::$reflections[$className];

        $productRef = new ReflectionClass($className);
        $propertiesRef = $productRef->getProperties();
        foreach ($propertiesRef as $propertyRef) {
            $propertyRef->setAccessible(true);
            self::$reflections[$className][$propertyRef->getName()] = $propertyRef;
        }
        return self::$reflections[$className];
    }

    public function set($object,$name,$value)
    {
        if(!is_object($object))
            throw new Exception\DomainException('setter is not exist for field "'.$name.'" on '.gettype($object));
        $reflections = self::getReflections(get_class($object));
        if(!isset($reflections[$name]))
            throw new Exception\DomainException('setter is not exist for field "'.$name.'" on '.get_class($object));
        $setter = $reflections[$name]->setValue($object,$value);
        return $this;
    }

    public function get($object,$name)
    {
        if(!is_object($object))
            throw new Exception\DomainException('getter is not exist for field "'.$name.'" on '.gettype($object));
        $reflections = self::getReflections(get_class($object));
        if(!isset($reflections[$name]))
            throw new Exception\DomainException('getter is not exist for field "'.$name.'" on '.get_class($object));
        return $reflections[$name]->getValue($object);
    }
}
