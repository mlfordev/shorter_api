<?php

namespace Phact\Orm\Aggregations;

class Min extends Aggregation
{
    public static function getSql($field)
    {
        return "MIN($field)";
    }
}