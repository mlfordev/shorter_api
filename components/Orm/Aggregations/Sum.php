<?php

namespace Phact\Orm\Aggregations;

class Sum extends Aggregation
{
    public static function getSql($field)
    {
        return "SUM($field)";
    }
}