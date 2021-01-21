<?php

namespace Phact\Orm\Configuration;

use Phact\Orm\ConnectionManagerInterface;

interface ConfigurationManagerInterface
{
    /**
     * @deprecated
     * @return int|null
     */
    public function getCacheFieldsTimeout(): ?int;
    public function getCacheQueryTimeout(): ?int;
    public function getConnectionManager(): ConnectionManagerInterface;
}