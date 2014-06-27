<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Context;

use \Magento\Payment\Model\Info;

/**
 * Class QuoteAdapter
 * @package Magento\Payment\Model\Method\Contex
 */
class QuoteAdapter implements AdapterInterface
{

    /**
     * @var Info
     */
    private $_paymentInfo;

    /**
     * @param Info $paymentInfo
     */
    public function __construct(Info $paymentInfo)
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
        return $this->_paymentInfo->getQuote()->getBillingAddress()->getCountryId();
    }

    /**
     * Returns context order id
     *
     * @return string
     */
    public function getOrderId()
    {
        if (!$this->_paymentInfo->getQuote()->getReservedOrderId()) {
            $this->_paymentInfo->getQuote()->reserveOrderId();
        }
        return $this->_paymentInfo->getQuote()->getReservedOrderId();
    }

    /**
     * Returns context grand total
     *
     * @return double
     */
    public function getBaseGrandTotal()
    {
        return (double)$this->_paymentInfo->getQuote()->getBaseGrandTotal();
    }

    /**
     * Returns context currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_paymentInfo->getQuote()->getBaseCurrencyCode();
    }
}
