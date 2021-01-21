<?php

namespace Phact\Orm\Aggregations;

class Max extends Aggregation
{
    public static function getSql($field)
    {
        return "MAX($field)";
    }
}