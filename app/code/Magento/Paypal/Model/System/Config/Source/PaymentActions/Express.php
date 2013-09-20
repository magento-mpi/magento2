<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available paypal express payment actions
 */
class Magento_Paypal_Model_System_Config_Source_PaymentActions_Express
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = Mage::getModel('Magento_Paypal_Model_Config');
        $configModel->setMethod(Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        return $configModel->getPaymentActions();
    }
}
