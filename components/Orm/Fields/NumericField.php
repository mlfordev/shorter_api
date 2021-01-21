<?php

namespace Phact\Orm\Fields;

abstract class NumericField extends Field
{
    public function getBlankValue()
    {
        return 0;
    }

    public function getValue($aliasConfig = null)
    {
        return is_null($this->_attribute) ? null : (int) $this->_attribute;
    }

    public function dbPrepareValue($value)
    {
        return (int) $value;
    }
}