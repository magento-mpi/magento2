<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

class TaxDetails extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_SUBTOTAL = 'subtotal';

    const KEY_TAX_AMOUNT = 'tax_amount';

    const KEY_TAXABLE_AMOUNT = 'taxable_amount';

    const KEY_DISCOUNT_AMOUNT = 'discount_amount';

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
     * Get taxable amount
     *
     * @return float
     */
    public function getTaxableAmount()
    {
        return $this->_get(self::KEY_TAXABLE_AMOUNT);
    }

    /**
     * Get discount amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->_get(self::KEY_DISCOUNT_AMOUNT);
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
