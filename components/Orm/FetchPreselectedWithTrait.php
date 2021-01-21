<?php

namespace Phact\Orm;

/**
 * Proxy for Many fields managers with access to with-data from model
 *
 * Trait FetchPreselectedWithTrait
 * @package Phact\Orm
 */
trait FetchPreselectedWithTrait
{
    protected function getWithData()
    {
        if (
            $this->ownerModel &&
            $this->getIsCleanSelection()
        ) {
            $fetchName = $this->fieldName . ($this->_activeSelection ? '->' . $this->_activeSelection : '');
            return $this->ownerModel->getWithData($fetchName);
        }
        return null;
    }

    public function all()
    {
        if (($data = $this->getWithData()) && ($data !== null)) {
            return $data;
        }
        return parent::all();
    }

    public function get()
    {
        if (($data = $this->getWithData()) && ($data !== null)) {
            return is_array($data) && isset($data[0]) ? $data[0] : null;
        }
        return parent::get();
    }
}