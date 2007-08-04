<?php

class Mage_Sales_Model_Entity_Quote_Attribute_Backend_Child
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if ($object->getQuote()) {
            $object->setParentId($object->getQuote()->getId());
        }
        parent::beforeSave($object);
        return $this;
    }
}