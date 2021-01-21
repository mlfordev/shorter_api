<?php

namespace Phact\Orm\Having;

use Phact\Orm\Aggregations\Aggregation;

class Having
{
    /**
     * @var Aggregation
     */
    protected $_aggregation;

    /**
     * @var string
     */
    protected $_condition;

    public function __construct(Aggregation $aggregation, $condition)
    {
        $this->_aggregation = $aggregation;
        $this->_condition = $condition;
    }

    public function getAggregation()
    {
        return $this->_aggregation;
    }

    public function getCondition()
    {
        return $this->_condition;
    }
}