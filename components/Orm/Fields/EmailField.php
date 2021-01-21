<?php

namespace Phact\Orm\Fields;

class EmailField extends CharField
{
    public function getFormField()
    {
        return $this->setUpFormField([
            'class' => \Phact\Form\Fields\EmailField::class
        ]);
    }
}