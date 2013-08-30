<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Config_Source_Allowedmethods
    extends Magento_Payment_Model_Config_Source_Allmethods
{
    protected function _getPaymentMethods()
    {
        return Mage::getSingleton('Magento_Payment_Model_Config')->getActiveMethods();
    }
}
