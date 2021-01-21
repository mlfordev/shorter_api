<?php

namespace Phact\Orm\Fields;

use Phact\Form\Fields\CharField;
use Phact\Form\Fields\DropDownField;

use Phact\Helpers\SmartProperties;

/**
 * Class Field
 *
 * @property string $name
 * @property mixed $attribute
 *
 * @package Phact\Orm\Fields
 */
abstract class Field
{
    use SmartProperties;

    public $pk = false;

    protected $_ownerModelClass;

    /**
     * @var \Phact\Orm\Model
     */
    protected $_model;

    protected $_name;

    protected $_attribute;

    protected $_oldAttribute;

    /**
     * Can field be NULL
     * @var bool
     */
    public $null = false;

    /**
     * Can field be blank/empty
     * @var bool
     */
    public $blank = false;

    /**
     * Unsigned operator for table column
     * @var bool
     */
    public $unsigned = false;

    /**
     * Zerofill operator for table column
     * @var bool
     * @deprecated
     */
    public $zerofill = false;

    /**
     * @var mixed
     */
    public $default = null;

    /**
     * Autoincrement
     * @var bool
     */
    public $autoincrement = false;

    /**
     * Table column length attribute
     * @var null
     */
    public $length = null;
    /**
     * @var array
     */
    public $choices = [];

    /**
     * Can edit with forms
     * @var bool
     */
    public $editable = true;

    /**
     * Label
     * @var bool
     */
    public $label = '';

    /**
     * Help text
     * @var bool
     */
    public $hint = '';

    /**
     * Has field attribute in model table
     * @var bool
     */
    public $virtual = false;

    /**
     * @var bool
     */
    public $rawSet = false;
    /**
     * @var bool
     */
    public $rawGet = false;
    /**
     * @return string
     */
    public function getBlankValue()
    {
        return '';
    }

    public function rawAccessValue($value)
    {
        return $value;
    }

    public function setOwnerModelClass($modelClass)
    {
        $this->_ownerModelClass = $modelClass;
    }

    public function getOwnerModelClass()
    {
        return $this->_ownerModelClass;
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {
        return $this->_name;
    }

    /**
     * @return bool
     */
    public function hasDbAttribute()
    {
        return (bool) $this->getAttributeName();
    }

    /**
     * Calls only in internal methods of Model
     * such as:
     *
     * _beforeInsert()
     * _afterInsert()
     * _beforeUpdate()
     * _afterUpdate()
     * _beforeDelete()
     * _afterDelete()
     *
     * @param $value
     */
    public function setAttribute($value)
    {
        $this->_attribute = $value;
    }

    public function setOldAttribute($value)
    {
        $this->_oldAttribute = $value;
    }

    public function getOldAttribute()
    {
        return $this->_oldAttribute;
    }

    public function getIsChanged()
    {
        $attr = $this->_attribute;
        $oldAttr = $this->_oldAttribute;

        if (is_numeric($attr) && is_numeric($oldAttr)) {
            return $attr != $oldAttr;
        }

        return $attr !== $oldAttr;
    }

    /**
     * Set model attribute
     *
     * @param $value
     */
    public function setModelAttribute($value)
    {
        $this->getModel()->setAttribute($this->getAttributeName(), $value);
    }

    /**
     * Has model attribute
     *
     * @return bool
     */
    public function hasModelAttribute()
    {
        return $this->getModel()->hasAttribute($this->getAttributeName());
    }

    /**
     * Get raw attribute name
     *
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    public function clean()
    {
        $this->_attribute = null;
        $this->_oldAttribute = null;
    }

    public function cleanAttribute()
    {
        $this->_attribute = null;
    }

    public function cleanOldAttribute()
    {
        $this->_oldAttribute = null;
    }

    /**
     * Get attribute prepared for model attributes
     *
     * @param null $aliasConfig
     * @return mixed
     */
    public function getValue($aliasConfig = null)
    {
        return $this->_attribute;
    }

    /**
     * Calls when Model::setAttribute() method called,
     * include calls like:
     *
     * $model->{attribute_name} = $value;
     *
     * @param $value
     * @param null $aliasConfig
     * @return mixed
     */
    public function setValue($value, $aliasConfig = null)
    {
        $this->_attribute = $value;
        return $this->_attribute;
    }

    /**
     * Value for writing to database
     */
    public function getDbPreparedValue()
    {
        $value = $this->getAttribute();
        if (is_null($value)) {
            if ($this->null) {
                return null;
            } elseif ($this->default) {
                return $this->default;
            } else {
                return $this->getBlankValue();
            }
        }
        return $this->dbPrepareValue($value);
    }

    public function setDefaultDbValue()
    {
        if ($this->hasDbAttribute()) {
            $this->setAttribute($this->getSafeValue());
        }
    }

    public function setFromDbValue($value)
    {
        $attribute = $this->attributePrepareValue($value);
        $this->setAttribute($attribute);
        $this->setOldAttribute($attribute);
    }

    public function getAdditionalFields()
    {
        return [];
    }

    public function beforeInsert()
    {
        if (!$this->autoincrement) {
            $this->setDefaultDbValue();
        }
    }

    public function beforeUpdate()
    {
    }

    public function beforeDelete()
    {
    }

    public function afterInsert()
    {
    }

    public function afterUpdate()
    {
    }

    public function afterDelete()
    {
    }

    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }


    /**
     * Prepare attribute value for database value
     * Reverse function for attributePrepareValue
     *
     * @param $value
     * @return mixed
     */
    public function dbPrepareValue($value)
    {
        return $value;
    }

    /**
     * Prepare db database for model attribute
     * Reverse function for dbPrepareValue
     *
     * @param $value
     * @return mixed
     */
    public function attributePrepareValue($value)
    {
        return $value;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getSqlType()
    {
        return null;
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return array
     */
    public function getColumnOptions()
    {
        $options = [];
        if ($this->unsigned) {
            $options['unsigned'] = true;
        }
        if ($this->null) {
            $options['notnull'] = !$this->null;
        }
        if ($this->default) {
            $options['default'] = $this->default;
        }
        if ($this->autoincrement) {
            $options['autoincrement'] = true;
        }
        if (!is_null($this->length)) {
            $options['length'] = $this->length;
        }
        return $options;
    }

    /**
     * Getting config form field
     * @return null
     */
    public function getFormField()
    {
        return $this->setUpFormField();
    }

    /**
     * Getting display representations of value
     * @param null $default
     * @return mixed|null
     */
    public function getChoiceDisplay($default = null)
    {
        $attribute = $this->getAttribute();
        if ($this->choices && isset($this->choices[$attribute])) {
            return $this->choices[$attribute];
        }
        return $default;
    }

    /**
     * Is required form field value
     * @return bool
     */
    public function getIsRequired()
    {
        return !$this->null && !$this->blank && is_null($this->default);
    }

    /**
     * Setting up form field
     *
     * @param array $config
     * @return null
     */
    public function setUpFormField($config = [])
    {
        if (!$this->editable) {
            return null;
        }

        $class = isset($config['class']) ? $config['class'] : null;

        if ($this->choices && !$class) {
            $class = DropDownField::class;
        }

        if (!$class) {
            $class = CharField::class;
        }

        return array_merge([
            'class' => $class,
            'required' => $this->getIsRequired(),
            'label' => $this->label,
            'hint' => $this->hint,
            'value' => $this->default,
            'choices' => $this->choices
        ], $config);
    }

    public function getSafeValue()
    {
        if (is_null($this->_attribute) && !$this->null) {
            if (!is_null($this->default)) {
                return $this->default;
            }

            return $this->getBlankValue();
        }
        return $this->_attribute;
    }
}