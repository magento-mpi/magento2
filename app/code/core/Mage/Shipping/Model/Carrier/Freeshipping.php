<?php

/**
 * Free shipping model
 *
 * @package    Mage
 * @subpackage Mage_Usa
 * @author     Sergiy Lysak <sergey@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Shipping_Model_Carrier_Freeshipping extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/freeshipping/active')) {
            return false;
        }
        
        $result = Mage::getModel('shipping/rate_result');
        
        $allow = ($request->getFreeShipping())
        	|| ($request->getPackageValue() >= Mage::getStoreConfig('carriers/freeshipping/cutoff_cost'));
        
        if ($allow) {
	    	$method = Mage::getModel('shipping/rate_result_method');
	    	
	    	$method->setCarrier('freeshipping');
	    	$method->setCarrierTitle(Mage::getStoreConfig('carriers/freeshipping/title'));
	    	
	    	$method->setMethod('freeshipping');
	    	$method->setMethodTitle(Mage::getStoreConfig('carriers/freeshipping/name'));
	    	
	    	$method->setPrice('0.00');
	    	$method->setCost('0.00');
    	
    	    $result->append($method);
        }
        
    	return $result;
    }
}
