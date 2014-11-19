<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface TaxRuleInterface extends ExtensibleDataInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get tax rule code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Get sort order.
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get customer tax class id
     *
     * @return int[]
     */
    public function getCustomerTaxClassIds();

    /**
     * Get product tax class id
     *
     * @return int[]
     */
    public function getProductTaxClassIds();

    /**
     * Get tax rate ids
     *
     * @return int[]
     */
    public function getTaxRateIds();

    /**
     * Get calculate subtotal.
     *
     * @return bool|null
     */
    public function getCalculateSubtotal();
}
