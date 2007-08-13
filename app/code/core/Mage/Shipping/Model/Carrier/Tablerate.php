<?php

class Mage_Shipping_Model_Carrier_Tablerate extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_conditionName = 'package_weight';
    
    public function setConditionName($name)
    {
        $this->_conditionName = $name;
    }
    
	/**
	 * Enter description here...
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/tablerate/active')) {
            return false;
        }
        
        if (!$request->getConditionName()) {
            $request->setConditionName($this->_conditionName);
        }

        $result = Mage::getModel('shipping/rate_result');
        $rate = $this->getRate($request);
        if (!empty($rate)) {
	    	$method = Mage::getModel('shipping/rate_result_method');

	    	$method->setCarrier('tablerate');
	    	$method->setCarrierTitle(Mage::getStoreConfig('carriers/tablerate/title'));

	    	$method->setMethod('bestway');
	    	$method->setMethodTitle(Mage::getStoreConfig('carriers/tablerate/name'));

	    	$method->setPrice($rate['price']);
	    	$method->setCost($rate['cost']);

    	    $result->append($method);
        }
        
    	return $result;
    }
    
    public function getRate(Mage_Shipping_Model_Rate_Request $request)
    {
        return Mage::getResourceModel('shipping/carrier_tablerate')->getRate($request);
    }
}
