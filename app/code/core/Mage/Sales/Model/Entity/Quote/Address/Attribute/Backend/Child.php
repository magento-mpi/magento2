<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Child
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if ($object->getAddress()) {
            $object->setParentId($object->getAddress()->getId());
        }
        parent::beforeSave($object);
        return $this;
    }
}