<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * Class TaxRule
 */
class TaxRule extends AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ID = 'id';

    const CODE = 'code';

    const CUSTOMER_TAX_CLASS_ID = 'customer_tax_class_id';

    const PRODUCT_TAX_CLASS_ID = 'product_tax_class_id';

    const TAX_RATES = 'tax_rates';

    const PRIORITY = 'priority';

    const SORT_ORDER = 'sort_order';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
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
     * @return int
     */
    public function getCustomerTaxClassId()
    {
        return $this->_get(self::CUSTOMER_TAX_CLASS_ID);
    }

    /**
     * Get product tax class id
     *
     * @return int
     */
    public function getProductTaxClassId()
    {
        return $this->_get(self::PRODUCT_TAX_CLASS_ID);
    }

    /**
     * Get tax rates
     *
     * @return Magento\Tax\Service\V1\Data\TaxRate[]| null
     */
    public function getTaxRates()
    {
        return $this->_get(self::TAX_RATES);
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
}
