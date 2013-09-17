<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Config_Source_Allmethods implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData = null;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData
    ) {
        $this->_paymentData = $paymentData;
    }

    public function toOptionArray()
    {
        return $this->_paymentData->getPaymentMethodList(true, true, true);
    }
}
