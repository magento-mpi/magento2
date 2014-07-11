<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data\TaxDetails;

class Item extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_CODE = 'code';

    const KEY_TYPE = 'type';

    const KEY_TAX_PERCENT = 'tax_percent';

    const KEY_PRICE = 'price';

    const KEY_PRICE_INCL_TAX = 'price_incl_tax';

    const KEY_ROW_TOTAL = 'row_total';

    const KEY_ROW_TOTAL_INCL_TAX = 'row_total_incl_tax';

    const KEY_ROW_TAX = 'row_tax';

    const KEY_TAXABLE_AMOUNT = 'taxable_amount';

    const KEY_DISCOUNT_AMOUNT = 'discount_amount';

    const KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'discount_tax_compensation_amount';

    const KEY_APPLIED_TAXES = 'applied_taxes';
    /**#@-*/

    /**
     * Get code (sku or shipping code)
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->_get(self::KEY_CODE);
    }

    /**
     * Get type (shipping, product, weee, gift wrapping, etc
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::KEY_TYPE);
    }

    /**
     * Get tax_percent
     *
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->_get(self::KEY_TAX_PERCENT);
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::KEY_PRICE);
    }

    /**
     * Get price including tax
     *
     * @return float
     */
    public function getPriceInclTax()
    {
        return $this->_get(self::KEY_PRICE_INCL_TAX);
    }

    /**
     * Get row total
     *
     * @return float
     */
    public function getRowTotal()
    {
        return $this->_get(self::KEY_ROW_TOTAL);
    }

    /**
     * Get row total including tax
     *
     * @return float
     */
    public function getRowTotalInclTax()
    {
        return $this->_get(self::KEY_ROW_TOTAL_INCL_TAX);
    }

    /**
     * Get row tax amount
     *
     * @return float
     */
    public function getRowTax()
    {
        return $this->_get(self::KEY_ROW_TAX);
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
     * Get discount tax compensation amount
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
}
