<?php
namespace Rindow\Stdlib\Entity;

interface Entity {
    public function hydrate(array $data);
    public function extract(array $keys=null);
}