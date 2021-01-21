<?php

namespace Phact\Orm;


use Doctrine\DBAL\Connection;

interface ConnectionManagerInterface
{
    /**
     * Get default connection name
     *
     * @return string
     */
    public function getDefaultConnection(): string;

    /**
     * Set connections configuration
     *
     * @param array $config
     * @return mixed
     */
    public function setConnections($config = []);

    /**
     * Get connection by name
     *
     * @param null $name
     * @return Connection
     */
    public function getConnection($name = null): Connection;
}