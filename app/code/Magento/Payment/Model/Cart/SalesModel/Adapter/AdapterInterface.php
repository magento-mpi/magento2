<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adapter interface for accessing sales model data
 */
namespace Magento\Payment\Model\Cart\SalesModel\Adapter;

interface AdapterInterface
{
    /**
     * Return sales model entity instance
     *
     * @return \Magento\Sales\Model\Order|\Magento\Sales\Model\Quote
     */
    public function getOriginalModel();

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
     * @return float|null
     */
    public function getBaseCustomerBalanceAmount();
}
