<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Model_Config_Source_Allmethods implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $methods = Mage::helper('Mage_Payment_Helper_Data')->getPaymentMethodList(true, true, true);
        return $methods;
    }
}
