<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Model_System_Config_Source_Payment_Allmethods
{
    public function toOptionArray()
    {
        $methods = Mage::helper('Mage_Payment_Helper_Data')->getPaymentMethodList(true, true, true);
        return $methods;
    }
}
