<?php
namespace AcmeTest\Entity;

use Rindow\Stdlib\Entity\EntityTrait;
use Rindow\Stdlib\Entity\Entity;

class Bean2 implements Entity
{
    use EntityTrait;

    protected $id;
    protected $name;
    private   $privateVar;
    public function getId()
    {
        return $this->id;
    }
    // a getter is not defined for name
}
