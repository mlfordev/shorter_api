<?php

namespace Phact\Orm\Adapters;

class SqliteAdapter extends Adapter
{
    /**
     * User-defined function for REGEXP support
     *
     * @param $pattern
     * @param $data
     * @param string $delimiter
     * @param string $modifiers
     * @return bool|null
     */
    public static function udfRegexp($pattern, $data, $delimiter = '~', $modifiers = 'isuS')
    {
        if (isset($pattern, $data))
        {
            return (preg_match(sprintf('%1$s%2$s%1$s%3$s', $delimiter, $pattern, $modifiers), $data) > 0);
        }
        return null;
    }
}