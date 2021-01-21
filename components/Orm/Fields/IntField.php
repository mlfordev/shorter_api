<?php

namespace Phact\Orm\Fields;

class IntField extends NumericField
{
    public $length = 11;

    public $rawGet = true;

    public $rawSet = true;

    public function attributePrepareValue($value)
    {
        return isset($value) ? (int) $value : null;
    }

    public function getValue($aliasConfig = null)
    {
        return $this->_attribute;
    }

    public function dbPrepareValue($value)
    {
        return (int) $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'integer';
    }
}