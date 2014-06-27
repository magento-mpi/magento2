<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Context;

use \Magento\Sales\Model\Order\Payment;

/**
 * Class OrderAdapter
 * @package Magento\Payment\Model\Method\Context
 */
class OrderAdapter implements AdapterInterface
{

    /**
     * @var Payment
     */
    private $_paymentInfo;

    /**
     * @param Payment $paymentInfo
     */
    public function __construct(Payment $paymentInfo)
    {
        $this->_paymentInfo = $paymentInfo;
    }

    /**
     * Returns context country id
     *
     * @return string
     */
    public function getCountryId()
    {
        return $this->_paymentInfo->getOrder()->getBillingAddress()->getCountryId();
    }

    /**
     * Returns context order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->_paymentInfo->getOrder()->getIncrementId();
    }

    /**
     * Returns context grand total
     *
     * @return double
     */
    public function getBaseGrandTotal()
    {
        return (double)$this->_paymentInfo->getOrder()->getQuoteBaseGrandTotal();
    }

    /**
     * Returns context currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_paymentInfo->getOrder()->getBaseCurrencyCode();
    }
}
