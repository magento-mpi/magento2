<?php

class Mage_Sales_Model_Entity_Invoice_Attribute_Backend_Child
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if ($object->getInvoice()) {
            $object->setParentId($object->getInvoice()->getId());
        }
        parent::beforeSave($object);
        return $this;
    }
}