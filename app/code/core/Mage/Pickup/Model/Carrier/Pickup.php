<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Pickup
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Pickup_Model_Carrier_Pickup extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/pickup/active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');
        
        if (Mage::getStoreConfig('carriers/pickup/type') == 'O') { // per order
            $shippingPrice = 555;
        } elseif (Mage::getStoreConfig('carriers/pickup/type') == 'I') { // per item
            $shippingPrice = $request->getPackageQty() * 666;
        } else {
            $shippingPrice = 111;
        }
        
        if ($shippingPrice) {
	    	$method = Mage::getModel('shipping/rate_result_method');
	    	
	    	$method->setCarrier('pickup');
	    	$method->setCarrierTitle(Mage::getStoreConfig('carriers/pickup/title'));
	    	
	    	$method->setMethod('pickup');
	    	$method->setMethodTitle(Mage::getStoreConfig('carriers/pickup/name'));
	    	
	    	$method->setPrice($shippingPrice);
	    	$method->setCost($shippingPrice);
    	
    	    $result->append($method);
        }
        
    	return $result;
    }
}
?>
