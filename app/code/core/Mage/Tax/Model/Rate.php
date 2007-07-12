<?php
class Mage_Tax_Model_Rate extends Varien_Object
{
    public function getResource()
    {
        return Mage::getResourceModel('tax/rate');
    }

    public function load($rateId)
    {
        return $this->getResource()->load($rateId);
    }

    public function loadWithAttributes($rateId)
    {
        return $this->getResource()->loadWithAttributes($rateId);
    }

    public function save($rateObject)
    {
        return $this->getResource()->save($rateObject);
    }

    public function delete($rateObject)
    {
        return $this->getResource()->delete($rateObject);
    }
}