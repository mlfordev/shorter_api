<?php

namespace Phact\Orm;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Phact\Exceptions\InvalidConfigException;
use Phact\Exceptions\UnknownPropertyException;
use Phact\Helpers\SmartProperties;

class ConnectionManager implements ConnectionManagerInterface
{
    use SmartProperties;

    protected $_connections;
    protected $_connectionsConfig;

    public $defaultConnection = 'default';

    private $container;

    public function getDefaultConnection(): string
    {
        return $this->defaultConnection;
    }

    public function setConnections($config = [])
    {
        $this->_connectionsConfig = $config;
    }

    /**
     * @param null $name
     * @return \Doctrine\DBAL\Connection
     * @throws UnknownPropertyException
     * @throws \Doctrine\DBAL\DBALException
     * @throws InvalidConfigException
     */
    public function getConnection($name = null): Connection
    {
        if (!$name) {
            $name = $this->getDefaultConnection();
        }
        if (!isset($this->_connections[$name])) {
            if (isset($this->_connectionsConfig[$name])) {
                $params = $this->_connectionsConfig[$name];

                $configuration = new Configuration();

                /** @var Connection $connection */
                $connection = DriverManager::getConnection($params, $configuration);
                $this->_connections[$name] = $connection;
            } else {
                throw new UnknownPropertyException("Connection with name '{$name}' not found");
            }
        }
        return $this->_connections[$name];
    }
}