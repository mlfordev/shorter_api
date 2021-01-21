<?php

namespace Phact\Orm\Aggregations;

class Avg extends Aggregation
{
    public static function getSql($field)
    {
        return "AVG($field)";
    }
}