<?php

class Mage_Eav_Model_Entity_Attribute_Backend_Store extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    protected function _beforeSave($object)
    {
        if (!$object->getData($this->getAttribute()->getName())) {
            $object->setData($this->getAttribute()->getName(), Mage::getSingleton('core/store')->getId());
        }
    }
}