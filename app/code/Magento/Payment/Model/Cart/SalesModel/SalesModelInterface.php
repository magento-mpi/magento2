<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wrapper interface for accessing sales model data
 */
namespace Magento\Payment\Model\Cart\SalesModel;

interface SalesModelInterface
{
    /**
     * Get all items from shopping sales model
     *
     * @return array
     */
    public function getAllItems();

    /**
     * @return float|null
     */
    public function getBaseSubtotal();

    /**
     * @return float|null
     */
    public function getBaseTaxAmount();

    /**
     * @return float|null
     */
    public function getBaseShippingAmount();

    /**
     * @return float|null
     */
    public function getBaseDiscountAmount();

    /**
     * Wrapper for \Magento\Object getDataUsingMethod method
     *
     * @param string $key
     * @param mixed $args
     * @return mixed
     */
    public function getDataUsingMethod($key, $args = null);

    /**
     * Return object that contains tax related fields
     *
     * @return \Magento\Sales\Model\Order|\Magento\Sales\Model\Quote\Address
     */
    public function getTaxContainer();
}
