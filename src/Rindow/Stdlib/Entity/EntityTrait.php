<?php
namespace Rindow\Stdlib\Entity;

trait EntityTrait
{
    public function __call($name, $arguments)
    {
        if(strlen($name)>3) {
            if(substr($name, 0, 3) == 'get') {
                $property = lcfirst(substr($name, 3));
                if(property_exists($this, $property)) {
                    return $this->$property;
                }
                throw new Exception\DomainException('Property "'.$property.'" is not found in '.get_class($this).'"');
            } else if(substr($name, 0, 3) == 'set') {
                $property = lcfirst(substr($name, 3));
                if(property_exists($this, $property)) {
                    if(!method_exists($this, 'get'.ucfirst($property))) {
                        $this->$property = $arguments[0];
                        return $this;
                    }
                    throw new Exception\DomainException('a property is read only:'.$property.' in '.get_class($this));
                }
                throw new Exception\DomainException('Property "'.$property.'" is not found in '.get_class($this).'"');
            }
        }
        throw new Exception\DomainException('Method "'.$name.'" is not found in "'.get_class($this).'"');
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            if(property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    public function extract(array $keys=null)
    {
        if($keys==null)
            return get_object_vars($this);
        $result = array();
        $properties = get_object_vars($this);
        foreach ($keys as $key) {
            if(array_key_exists($key,$properties)) {
                $result[$key] = $this->$key;
            }
        }
        return $result;
    }
    
    public function __set($name,$value)
    {
        if(!property_exists($this, '__propertyAccess') ||
            !isset($this->__propertyAccess[$name]) ||
            !property_exists($this, $name)) {
            throw new Exception\DomainException('Invalid proparty "'.$name.'" in '.get_class($this));
        }
        $setter = 'set'.ucfirst($name);
        if(method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }
        $getter = 'get'.ucfirst($name);
        if(method_exists($this, $getter))
            throw new Exception\DomainException('a property is read only:'.$name);
        $this->$name = $value;
    }

    public function __get($name)
    {
        if(!property_exists($this, '__propertyAccess') ||
            !isset($this->__propertyAccess[$name]) ||
            !property_exists($this, $name)) {
            throw new Exception\DomainException('Invalid proparty "'.$name.'" in '.get_class($this));
        }
        $getter = 'get'.ucfirst($name);
        if(method_exists($this, $getter))
            return $this->$getter();
        return $this->$name;
    }
}