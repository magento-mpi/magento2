<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Parent
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object)
    {
        parent::afterSave($object);
        
        $object->getItemsCollection()->save();
        $object->getShippingRatesCollection()->save();
        
        return $this;
    }
}