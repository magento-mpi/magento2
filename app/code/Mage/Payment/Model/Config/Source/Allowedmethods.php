<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Model_Config_Source_Allowedmethods
    extends Mage_Payment_Model_Config_Source_Allmethods
{
    protected function _getPaymentMethods()
    {
        return Mage::getSingleton('Mage_Payment_Model_Config')->getActiveMethods();
    }
}
