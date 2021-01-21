<?php

namespace Phact\Orm\Fields;

class DateTimeField extends DateField
{
    /**
     * Value in the range from 0 to 6 may be given to specify fractional seconds precision
     * @var int
     */
    public $fsp = 0;

    public $format = 'Y-m-d H:i:s';

    public function getBlankValue()
    {
        return '0000-00-00 00:00:00';
    }

    public function getType()
    {
        return "datetime";
    }
}