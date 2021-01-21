<?php

namespace Phact\Orm\Aggregations;

class CountDistinct extends Aggregation
{
    public static function getSql($field)
    {
        return "COUNT(DISTINCT $field)";
    }
}