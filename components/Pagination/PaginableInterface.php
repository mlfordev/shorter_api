<?php

namespace Phact\Pagination;

interface PaginableInterface
{
    public function setPaginationLimit($limit): PaginableInterface;

    public function setPaginationOffset($offset): PaginableInterface;

    public function getPaginationTotal();

    public function getPaginationData($dataType = null);
}