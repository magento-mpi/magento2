<?php

class Mage_Eav_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute_Abstract
{
    protected function _getDefaultBackendModel()
    {
        switch ($this->getAttributeName()) {
            case 'created_at':
                return 'eav/entity_attribute_backend_time_created';

            case 'updated_at':
                return 'eav/entity_attribute_backend_time_updated';

            case 'store_id':
                return 'eav/entity_attribute_backend_store';
        }

        return parent::_getDefaultBackendModel();
    }

    protected function _getDefaultFrontendModel()
    {
        return parent::_getDefaultFrontendModel();
    }

    protected function _getDefaultSourceModel()
    {
        switch ($this->getAttributeName()) {
            case 'store_id':
                return 'eav/entity_attribute_source_store';
        }
        return parent::_getDefaultSourceModel();
    }

    public function itemExists()
    {
        return $this->getResource()->itemExists($this);
    }

    public function saveAttributes()
    {
        return $this->getResource()->saveAttributes($this);
    }
}