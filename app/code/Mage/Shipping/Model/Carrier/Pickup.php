<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Carrier_Pickup
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'pickup';
    protected $_isFixed = true;

    /**
     * Enter description here...
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('Mage_Shipping_Model_Rate_Result');

        if (!empty($rate)) {
            $method = Mage::getModel('Mage_Shipping_Model_Rate_Result_Method');

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
