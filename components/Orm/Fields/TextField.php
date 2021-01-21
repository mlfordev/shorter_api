<?php

namespace Phact\Orm\Fields;

use Phact\Form\Fields\TextAreaField;

class TextField extends CharField
{
    public $length = null;

    public function getType()
    {
        return "text";
    }

    public function getFormField()
    {
        return $this->setUpFormField([
            'class' => TextAreaField::class
        ]);
    }
}