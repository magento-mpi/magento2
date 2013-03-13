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
 * Source model for available paypal express payment actions
 */
class Mage_Paypal_Model_System_Config_Source_PaymentActions_Express
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('Mage_Paypal_Model_Config');
        $configModel->setMethod(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        return $configModel->getPaymentActions();
    }
}
