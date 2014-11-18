<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * previous implementation @see \Magento\Tax\Service\V1\Data\TaxRule
 */
interface TaxRuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ID = 'id';

    const CODE = 'code';

    const CUSTOMER_TAX_CLASS_IDS = 'customer_tax_class_ids';

    const PRODUCT_TAX_CLASS_IDS = 'product_tax_class_ids';

    const TAX_RATE_IDS = 'tax_rate_ids';

    const PRIORITY = 'priority';

    const SORT_ORDER = 'sort_order';

    const CALCULATE_SUBTOTAL = 'calculate_subtotal';
    /**#@-*/

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
     * Get calculate subtotal.
     *
     * @return bool|null
     */
    public function getCalculateSubtotal();
}
