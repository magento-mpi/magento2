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
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData
    ) {
        $this->_paymentData = $paymentData;
    }

    public function toOptionArray()
    {
        return $this->_paymentData->getPaymentMethodList(true, true, true);
    }
}
