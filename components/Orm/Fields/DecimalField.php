<?php

namespace Phact\Orm\Fields;

class DecimalField extends NumericField
{
    public $rawGet = true;

    public $rawSet = true;
    /**
     * Total number of digits
     * @var int
     */
    public $precision = 10;

    /**
     * Number of digits after the decimal point
     * @var int
     */
    public $scale = 2;

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

    public function mainSqlType()
    {
        return "decimal({$this->precision}, {$this->scale})";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "decimal";
    }

    public function getColumnOptions()
    {
        $options = parent::getColumnOptions();
        if ($this->precision) {
            $options['precision'] = $this->precision;
        }
        if ($this->scale) {
            $options['scale'] = $this->scale;
        }
        return $options;
    }
}
