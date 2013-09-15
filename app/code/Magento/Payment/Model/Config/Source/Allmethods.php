<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Config\Source;

class Allmethods implements \Magento\Core\Model\Option\ArrayInterface
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
