<?php

namespace Phact\Orm\Adapters;

class PostgresqlAdapter
{
    /**
     * @return string
     */
    public static function getRegexpExpression()
    {
        return "~";
    }
}