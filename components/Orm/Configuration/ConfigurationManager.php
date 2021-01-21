<?php

namespace Phact\Orm\Configuration;

use Phact\Event\EventManagerInterface;
use Phact\Orm\ConnectionManagerInterface;

class ConfigurationManager implements ConfigurationManagerInterface
{
    /**
     * @var ConnectionManagerInterface
     */
    protected $_connectionManager;

    /**
     * @var EventManagerInterface|null
     */
    protected $_eventManager;

    /**
     * @var int
     */
    protected $_cacheFieldsTimeout;

    /**
     * @var int
     */
    protected $_cacheQueryTimeout;


    public function __construct(ConnectionManagerInterface $connectionManager, EventManagerInterface $eventManager = null)
    {
        $this->_connectionManager = $connectionManager;
        $this->_eventManager = $eventManager;
    }

    /**
     * @param int|null $timeout
     * @return $this
     */
    public function setCacheFieldsTimeout(int $timeout = null): self
    {
        $this->_cacheFieldsTimeout = $timeout;
        return $this;
    }

    /**
     * @return int
     * @deprecated
     */
    public function getCacheFieldsTimeout(): ?int
    {
        return $this->_cacheFieldsTimeout;
    }

    /**
     * @param int $cacheQueryTimeout
     * @return self
     */
    public function setCacheQueryTimeout(?int $cacheQueryTimeout): self
    {
        $this->_cacheQueryTimeout = $cacheQueryTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheQueryTimeout(): ?int
    {
        return $this->_cacheQueryTimeout;
    }

    /**
     * @return ConnectionManagerInterface
     */
    public function getConnectionManager(): ConnectionManagerInterface
    {
        return $this->_connectionManager;
    }

    /**
     * @return null|EventManagerInterface
     */
    public function getEventManager(): ?EventManagerInterface
    {
        return $this->_eventManager;
    }
}