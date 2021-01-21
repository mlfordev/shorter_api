<?php

namespace App\Core;


use App\Core\Routers\CliRouter;
use App\Core\Routers\WebRouter;
use RuntimeException;

/**
 * Class Application
 * @package App\Core
 */
class Application
{
    /** @var Application */
    private static $instance;

    /**
     * @return Application
     */
    public static function getInstance(): Application
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return HttpResponse|void
     */
    public function run()
    {
        /** @var WebRouter|CliRouter $router */
        $routerClassName = 'App\Core\Routers\\' . $this->requestMode() . 'Router';
        $router = new $routerClassName();
        return $router->handleRequest();
    }

    /**
     * @return string
     */
    public function requestMode(): string
    {
        return $this->isCliMode() ? 'Cli' : 'Web';
    }

    /**
     * @return bool
     */
    public function isCliMode(): bool
    {
        return 'cli' === PHP_SAPI;
    }

    private function __construct() {}

    private function __clone() {}

    public function __wakeup()
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }
}