<?php

class Mage_Sales_Model_Entity_Invoice_Attribute_Backend_Parent
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object)
    {
        parent::afterSave($object);
        
        $object->getAddressesCollection()->save();
        $object->getItemsCollection()->save();
        $object->getPaymentsCollection()->save();
        $object->getShipmentsCollection()->save();
        
        return $this;
    }
}