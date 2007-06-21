<?php

class Mage_Catalog_Model_Product_Link extends Varien_Object 
{
    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/product_link');
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
