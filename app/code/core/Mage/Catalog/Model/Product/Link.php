<?php

class Mage_Catalog_Model_Product_Link extends Varien_Data_Object 
{
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'product_link');
        }
        return $resource;
    }

    public function load($linkId)
    {
        $this->setData($this->getResource()->load($linkId));
        return $this;
    }
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        return $this;
    }

}
