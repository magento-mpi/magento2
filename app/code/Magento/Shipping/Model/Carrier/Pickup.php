<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Carrier;

class Pickup
    extends \Magento\Shipping\Model\Carrier\AbstractCarrier
    implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'pickup';
    protected $_isFixed = true;

    /**
     * Enter description here...
     *
     * @param \Magento\Shipping\Model\Rate\Request $data
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Shipping\Model\Rate\Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = \Mage::getModel('Magento\Shipping\Model\Rate\Result');

        if (!empty($rate)) {
            $method = \Mage::getModel('Magento\Shipping\Model\Rate\Result\Method');

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
