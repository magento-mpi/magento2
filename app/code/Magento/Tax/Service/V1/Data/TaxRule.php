<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class TaxRule
 */
class TaxRule extends AbstractExtensibleObject
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
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get tax rule code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get customer tax class id
     *
     * @return int[]
     */
    public function getCustomerTaxClassIds()
    {
        return $this->_get(self::CUSTOMER_TAX_CLASS_IDS);
    }

    /**
     * Get product tax class id
     *
     * @return int[]
     */
    public function getProductTaxClassIds()
    {
        return $this->_get(self::PRODUCT_TAX_CLASS_IDS);
    }

    /**
     * Get tax rate ids
     *
     * @return int[]
     */
    public function getTaxRateIds()
    {
        return $this->_get(self::TAX_RATE_IDS);
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->_get(self::PRIORITY);
    }

    /**
     * Get sort order.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * Get calculate subtotal.
     *
     * @return bool|null
     */
    public function getCalculateSubtotal()
    {
        return $this->_get(self::CALCULATE_SUBTOTAL);
    }
}
