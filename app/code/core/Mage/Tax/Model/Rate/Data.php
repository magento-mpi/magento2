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
		if (!$this->getCustomerClassId()
			|| !$this->getProductClassId()
			|| !$this->getRegionId()
			|| !$this->getPostcode()) {
			throw Mage::exception('Mage_Tax', 'Invalid data for tax rate calculation');	
		}
		
		$cacheKey = $request->getCustomerClassId()
			.'|'.$request->getProductClassId()
			.'|'.$request->getRegionId()
			.'|'.$request->getPostcode()
			.'|'.$request->getCountyId();
			
		if (!isset($this->_cache[$cacheKey])) {
			$this->_cache[$cacheKey] = $this->getResource()->fetchRate($this);
		}
		
		return $this->_cache[$cacheKey];
	}

}