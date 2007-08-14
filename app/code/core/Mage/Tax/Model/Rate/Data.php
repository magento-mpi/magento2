<?php

/**
 * Enter description here...
 *
 * attributes:
 * - customer_class_id
 * - product_class_id
 * - region_id
 * - county_id
 * - postcode
 */
class Mage_Tax_Model_Rate_Data extends Mage_Core_Model_Abstract 
{
	protected $_cache = array();
	
	protected function _construct()
	{
		$this->_init('tax/rate_data');
	}
	
	public function getRate()
	{
		if (!$this->getPostcode()
			|| !$this->getRegionId()
			|| !$this->getCustomerClassId()
			|| !$this->getProductClassId()) {
			return 0;
			#throw Mage::exception('Mage_Tax', 'Invalid data for tax rate calculation');	
		}
		
		$cacheKey = $this->getCustomerClassId()
			.'|'.$this->getProductClassId()
			.'|'.$this->getRegionId()
			.'|'.$this->getPostcode()
			.'|'.$this->getCountyId();
			
		if (!isset($this->_cache[$cacheKey])) {
			$this->_cache[$cacheKey] = $this->getResource()->fetchRate($this);
		}
		
		return $this->_cache[$cacheKey];
	}

	public function getRegionId()
	{
		if (!$this->getData('region_id') && $this->getPostcode()) {
			$this->setRegionId(Mage::getModel('usa/postcode')->load($this->getPostcode())->getRegionId());
		}
		return $this->getData('region_id');
	}
	
	public function getCustomerClassId()
	{
		if (!$this->getData('customer_class_id')) {
			$this->setCustomerClassId(Mage::getSingleton('customer/session')->getCustomer()->getTaxClassId());
		}
		return $this->getData('customer_class_id');
	}
}