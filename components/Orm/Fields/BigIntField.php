<?php

namespace Phact\Orm\Fields;

class BigIntField extends IntField
{
    public $length = 20;
    public $unsigned = true;

    public function dbPrepareValue($value)
    {
        return (float) $value;
    }

    public function getType()
    {
        return 'bigint';
    }
}