<?php

namespace Phact\Orm\Configuration;

use Phact\Event\EventManager;
use Phact\Exceptions\InvalidConfigException;
use Phact\Orm\ConnectionManager;

/**
 * Proxy class singleton for configuration managing
 *
 * Class ConfigurationProvider
 * @package Phact\Orm\Configuration
 */
class ConfigurationProvider
{
    /**
     * @var ConfigurationManagerInterface
     */
    private $manager;

    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array[]
     */
    private static $config;

    /**
     * @return ConfigurationProvider
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            $connectionManager = new ConnectionManager();
            $eventManager = new EventManager();
            $connectionManager->setConnections(self::$config);
            self::$instance->manager = new ConfigurationManager($connectionManager, $eventManager);
        }
        return self::$instance;
    }

    public static function setDbConfig(array $config = []): void
    {
        self::$config = $config;
    }

    /**
     * @return ConfigurationManagerInterface
     * @throws InvalidConfigException
     */
    public function getManager(): ConfigurationManagerInterface
    {
        if (!$this->manager) {
            throw new InvalidConfigException('Please, provide correct ConfigurationManager at first');
        }
        return $this->manager;
    }

    private function __construct ()
    {
    }

    private function __clone ()
    {
    }
}