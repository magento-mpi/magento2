<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Method\Context;

/**
 * Interface AdapterInterface
 * @package Magento\Payment\Model\Method\Context
 */
interface AdapterInterface
{
    /**
     * Returns context country id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Returns context order id
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Returns context grand total
     *
     * @return double
     */
    public function getBaseGrandTotal();

    /**
     * Returns context currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode();
}
