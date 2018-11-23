<?php
namespace Rindow\Stdlib\Paginator;

use Countable;

interface PaginatorAdapter extends Countable
{
    public function getItems($offset, $itemMaxPerPage);
}