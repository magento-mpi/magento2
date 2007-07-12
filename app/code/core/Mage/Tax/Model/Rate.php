<?php
class Mage_Tax_Model_Rate extends Varien_Object
{
    public function __construct($rate=false)
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function getResource()
    {
        return Mage::getResourceModel('tax/rate');
    }

    public function load($rateId)
    {
        $this->getResource()->load($this, $rateId);
        return $this;
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