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
 * Source model for available payment actions
 */
class Magento_Paypal_Model_System_Config_Source_PaymentActions implements Magento_Core_Model_Option_ArrayInterface
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
        return $this->_configFactory->create()->getPaymentActions();
    }
}
