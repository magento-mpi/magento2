<?php

/**
 * Flat rate shipping model
 *
 * @package    Mage
 * @subpackage Mage_Usa
 * @author     Sergiy Lysak <sergey@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Shipping_Model_Carrier_Flatrate extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/flatrate/active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');
        if (Mage::getStoreConfig('carriers/flatrate/type') == 'O') { // per order
            $shippingPrice = Mage::getStoreConfig('carriers/flatrate/price');
        } elseif (Mage::getStoreConfig('carriers/flatrate/type') == 'I') { // per item
            $shippingPrice = $request->getPackageQty() * Mage::getStoreConfig('carriers/flatrate/price');
        } else {
            $shippingPrice = false;
        }
        
        if ($shippingPrice) {
	    	$method = Mage::getModel('shipping/rate_result_method');
	    	
	    	$method->setCarrier('flatrate');
	    	$method->setCarrierTitle(Mage::getStoreConfig('carriers/flatrate/title'));
	    	
	    	$method->setMethod('flatrate');
	    	$method->setMethodTitle(Mage::getStoreConfig('carriers/flatrate/name'));
	    	
	    	$method->setPrice($shippingPrice);
	    	$method->setCost($shippingPrice);
    	
    	    $result->append($method);
        }
        
    	return $result;
    }
}
