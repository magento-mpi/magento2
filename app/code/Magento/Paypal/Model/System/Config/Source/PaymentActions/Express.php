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
     * @var Magento_Paypal_Model_ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_Paypal_Model_ConfigFactory $configFactory
     */
    public function __construct(Magento_Paypal_Model_ConfigFactory $configFactory)
    {
        $this->_configFactory = $configFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Magento_Paypal_Model_Config $configModel */
        $configModel = $this->_configFactory->create();
        $configModel->setMethod(Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        return $configModel->getPaymentActions();
    }
}
