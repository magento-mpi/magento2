<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Config_Source_Allowedmethods
    extends Magento_Payment_Model_Config_Source_Allmethods
{
    /**
     * Payment config model
     *
     * @var Magento_Payment_Model_Config
     */
    protected $_paymentConfig;

    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Payment_Model_Config $paymentConfig
    ) {
        parent::__construct($paymentData);
        $this->_paymentConfig = $paymentConfig;
    }

    protected function _getPaymentMethods()
    {
        return $this->_paymentConfig->getActiveMethods();
    }
}
