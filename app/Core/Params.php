<?php

namespace App\Core;


use BadFunctionCallException;
use RuntimeException;

/**
 * Class Params
 * @package App\Core
 */
class Params
{
    /**
     * @var array|bool
     */
    protected $variables = [];

    /**
     * @var object
     */
    private static $instance;

    /**
     * @param string $path
     * @return Params
     */
    public static function getInstance($path = PROJECT_ROOT . '/.env'): Params
    {
        if (!file_exists($path)) {
            throw new BadFunctionCallException('Отсутствует файл настроек приложения');
        }

        if (null === self::$instance) {
            self::$instance = new self();

            if (!(self::$instance->variables = parse_ini_file($path))) {
                throw new BadFunctionCallException('Не удалось загрузить настройки приложения');
            }
        }

        return self::$instance;
    }

    /**
     * @param $param
     * @return string
     */
    public function getValue(string $param): string
    {
        if (empty($this->variables[$param])) {
            throw new BadFunctionCallException('Параметр ' . $param . ' не задан в файле настроек приложения');
        }

        return $this->variables[$param];
    }

    private function __construct() {}

    private function __clone() {}

    public function __wakeup()
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }
}