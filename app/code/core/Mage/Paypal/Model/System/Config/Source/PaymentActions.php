<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available payment actions
 */
class Mage_Paypal_Model_System_Config_Source_PaymentActions
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('Mage_Paypal_Model_Config');
        return $configModel->getPaymentActions();
    }
}
