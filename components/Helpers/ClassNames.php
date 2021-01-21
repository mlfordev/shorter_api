<?php

namespace Phact\Helpers;

trait ClassNames
{
    public static function className()
    {
        return static::class;
    }

    public static function classNameShort()
    {
        return substr(static::class, strrpos(static::class, '\\')+1);
    }

    public static function classNamespace()
    {
        return substr(static::class, 0, strrpos(static::class, '\\'));
    }

    public static function classNameUnderscore()
    {
        return Text::camelCaseToUnderscores(static::classNameShort());
    }

    public static function getModuleName()
    {
        $classParts = explode('\\', static::class);
        if ($classParts[0] == 'Modules' && isset($classParts[1])) {
            return $classParts[1];
        }
        return null;
    }
}