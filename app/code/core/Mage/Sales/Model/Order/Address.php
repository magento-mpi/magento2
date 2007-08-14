<?php

class Mage_Sales_Model_Order_Address extends Mage_Core_Model_Abstract
{
    protected $_order;
    
    protected function _construct()
    {
        $this->_init('sales/order_address');
    }
    
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function importQuoteAddress(Mage_Sales_Model_Quote_Address $newAddress)
    {
        $address = clone $newAddress;
        $address->unsEntityId()
            ->unsAttributeSetId()
            ->unsEntityTypeId()
            ->unsParentId();
            
        $this->addData($address->getData());
        return $this;
    }
    
    public function getName()
    {
    	return $this->getFirstname().' '.$this->getLastname();
    }
    
    public function getRegion()
    {
    	if ($this->getData('region_id') && !$this->getData('region')) {
    		$this->setData('region', Mage::getModel('directory/region')->load($this->getData('region_id'))->getCode());
    	}
    	return $this->getData('region');
    }
    
    public function getCountry()
    {
    	if ($this->getData('country_id') && !$this->getData('country')) {
    		$this->setData('country', Mage::getModel('directory/country')->load($this->getData('country_id'))->getIso2Code());
    	}
    	return $this->getData('country');
    }
    
    public function getFormated($html=false)
    {
    	return Mage::getModel('directory/country')->load($this->getCountryId())->formatAddress($this, $html);
    }
}