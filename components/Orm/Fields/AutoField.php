<?php

namespace Phact\Orm\Fields;

class AutoField extends IntField
{
    public $pk = true;
    public $unsigned = true;
    public $editable = false;
    public $autoincrement = true;
}