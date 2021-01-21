<?php

namespace Phact\Helpers;
use Phact\Exceptions\InvalidConfigException;

/**
 * Helper class that create objects and configure it
 *
 * Class Configurator
 * @package Phact\Helpers
 */
class Configurator
{
    /**
     *
     * @param $class string|array
     * @param array $config array
     * @return mixed
     * @throws InvalidConfigException
     */
    public static function create($class, $config = [])
    {
        list($class, $config) = self::split($class, $config);
        $obj = new $class;
        $obj = self::configure($obj, $config);
        return $obj;
    }

    public static function split($class, $config = [])
    {
        if (is_array($class) && isset($class['class'])) {
            $config = $class;
            $class = $config['class'];
            unset($config['class']);
        } elseif (!is_string($class)) {
            throw new InvalidConfigException("Class name must be defined");
        }
        return [$class, $config];
    }

    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->{$name} = $value;
        }
        return $object;
    }
}