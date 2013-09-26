<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Config\Source;

class Allowedmethods
    extends \Magento\Payment\Model\Config\Source\Allmethods
{
    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * Construct
     *
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Payment\Model\Config $paymentConfig
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Payment\Model\Config $paymentConfig
    ) {
        parent::__construct($paymentData);
        $this->_paymentConfig = $paymentConfig;
    }

    protected function _getPaymentMethods()
    {
        return $this->_paymentConfig->getActiveMethods();
    }
}
