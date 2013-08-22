<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Carrier_Pickup
    extends Magento_Shipping_Model_Carrier_Abstract
    implements Magento_Shipping_Model_Carrier_Interface
{

    protected $_code = 'pickup';
    protected $_isFixed = true;

    /**
     * Enter description here...
     *
     * @param Magento_Shipping_Model_Rate_Request $data
     * @return Magento_Shipping_Model_Rate_Result
     */
    public function collectRates(Magento_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('Magento_Shipping_Model_Rate_Result');

        if (!empty($rate)) {
            $method = Mage::getModel('Magento_Shipping_Model_Rate_Result_Method');

            $method->setCarrier('pickup');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('store');
            $method->setMethodTitle(__('Store Pickup'));

            $method->setPrice(0);
            $method->setCost(0);

            $result->append($method);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('pickup'=>__('Store Pickup'));
    }

}
