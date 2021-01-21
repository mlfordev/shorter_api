<?php

namespace Phact\Orm\Fields;

class TimeField extends DateTimeField
{
    public $format = 'H:i:s';

    public function getBlankValue()
    {
        return '00:00:00';
    }

    public function getSqlType()
    {
        return "time";
    }
}