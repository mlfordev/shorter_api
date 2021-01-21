<?php

namespace Phact\Orm\Fields;

class CharField extends Field
{
    public $rawSet = true;

    public $rawGet = true;

    public $length = 255;

    public function attributePrepareValue($value)
    {
        return isset($value) ? (string) $value : null;
    }

    public function getValue($aliasConfig = null)
    {
        return $this->_attribute;
    }

    public function dbPrepareValue($value)
    {
        return (string) $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "string";
    }
}