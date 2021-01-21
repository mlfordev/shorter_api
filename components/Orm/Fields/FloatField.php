<?php

namespace Phact\Orm\Fields;

class FloatField extends NumericField
{
    public $rawGet = true;

    public $rawSet = true;

    public function attributePrepareValue($value)
    {
        return isset($value) ? (float) $value : null;
    }

    public function getValue($aliasConfig = null)
    {
        return $this->_attribute;
    }

    public function dbPrepareValue($value)
    {
        return (float) $value;
    }

    public function getType()
    {
        return "float";
    }
}