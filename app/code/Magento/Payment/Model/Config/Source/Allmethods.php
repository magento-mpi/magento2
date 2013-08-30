<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Config_Source_Allmethods implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $methods = Mage::helper('Magento_Payment_Helper_Data')->getPaymentMethodList(true, true, true);
        return $methods;
    }
}
