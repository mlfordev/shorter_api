<?php

namespace Phact\Orm;

use InvalidArgumentException;

class Q
{
    public static function andQ()
    {
        $args = func_get_args();
        return static::buildQ($args, 'and');
    }

    public static function orQ()
    {
        $args = func_get_args();
        return static::buildQ($args, 'or');
    }

    public static function notQ()
    {
        $args = func_get_args();
        return static::buildQ($args, 'not');
    }

    public static function buildQ($q, $condition)
    {
        if (!is_array($q)) {
            throw new InvalidArgumentException("Argument for methods andQ, orQ, notQ, buildQ must be an array");
        }
        return array_merge([$condition], $q);
    }
}