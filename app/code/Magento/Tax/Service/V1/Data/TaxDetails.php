<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

class TaxDetails extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_SUBTOTAL = 'subtotal';

    const KEY_TAX_AMOUNT = 'tax_amount';

    const KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'discount_tax_compensation_amount';

    const KEY_APPLIED_TAXES = 'applied_taxes';

    const KEY_ITEMS = 'items';

    /**#@-*/

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->_get(self::KEY_SUBTOTAL);
    }

    /**
     * Get tax amount
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->_get(self::KEY_TAX_AMOUNT);
    }

    /**
     * Get discount amount
     *
     * @return float
     */
    public function getDiscountTaxCompensationAmount()
    {
        return $this->_get(self::KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }

    /**
     * Get applied taxes
     *
     * @return \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax[] | null
     */
    public function getAppliedTaxes()
    {
        return $this->_get(self::KEY_APPLIED_TAXES);
    }

    /**
     * Get TaxDetails items
     *
     * @return \Magento\Tax\Service\V1\Data\TaxDetails\Item[] | null
     */
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS);
    }
}
