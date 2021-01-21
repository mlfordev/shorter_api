<?php

namespace Phact\Orm\Aggregations;

class Count extends Aggregation
{
    public static function getSql($field)
    {
        return "COUNT($field)";
    }
}