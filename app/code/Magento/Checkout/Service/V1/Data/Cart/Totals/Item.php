<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart\Totals;

/**
 * Cart item totals
 *
 * @codeCoverageIgnore
 */
class Item extends \Magento\Framework\Service\Data\AbstractObject
{
    /* ITEM DATA */
    const PRICE = 'price';
    const BASE_PRICE = 'base_price';
    const QTY = 'qty';

    /* ROW TOTALS */
    const ROW_TOTAL = 'row_total';
    const BASE_ROW_TOTAL = 'base_row_total';
    const ROW_TOTAL_WITH_DISCOUNT = 'row_total_with_discount';

    /* DISCOUNT */
    const DISCOUNT_AMOUNT = 'discount_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const DISCOUNT_PERCENT = 'discount_percent';

    /* TAX */
    const TAX_AMOUNT = 'tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const TAX_PERCENT = 'tax_percent';

    const PRICE_INCL_TAX = 'price_incl_tax';
    const BASE_PRICE_INCL_TAX = 'base_price_incl_tax';
    const ROW_TOTAL_INCL_TAX = 'row_total_incl_tax';
    const BASE_ROW_TOTAL_INCL_TAX = 'base_row_total_incl_tax';

    /**
     * Get item price in quote currency
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Get item price in base currency
     *
     * @return float
     */
    public function getBasePrice()
    {
        return $this->_get(self::BASE_PRICE);
    }

    /**
     * Get item qty
     *
     * @return int
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Get row total in quote currency
     *
     * @return float
     */
    public function getRowTotal()
    {
        return $this->_get(self::ROW_TOTAL);
    }

    /**
     * Get row total in base currency
     *
     * @return float
     */
    public function getBaseRowTotal()
    {
        return $this->_get(self::BASE_ROW_TOTAL);
    }

    /**
     * Get row total with discount in quote currency
     *
     * @return float|null
     */
    public function getRowTotalWithDiscount()
    {
        return $this->_get(self::ROW_TOTAL_WITH_DISCOUNT);
    }

    /**
     * Get tax amount in quote currency
     *
     * @return float|null
     */
    public function getTaxAmount()
    {
        return $this->_get(self::TAX_AMOUNT);
    }

    /**
     * Get tax amount in base currency
     *
     * @return float|null
     */
    public function getBaseTaxAmount()
    {
        return $this->_get(self::BASE_TAX_AMOUNT);
    }

    /**
     * Get tax percent
     *
     * @return int|null
     */
    public function getTaxPercent()
    {
        return $this->_get(self::TAX_PERCENT);
    }

    /**
     * Get discount amount in quote currency
     *
     * @return float|null
     */
    public function getDiscountAmount()
    {
        return $this->_get(self::DISCOUNT_AMOUNT);
    }

    /**
     * Get discount amount in base currency
     *
     * @return float|null
     */
    public function getBaseDiscountAmount()
    {
        return $this->_get(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Get discount percent
     *
     * @return int|null
     */
    public function getDiscountPercent()
    {
        return $this->_get(self::DISCOUNT_PERCENT);
    }

    /**
     * Get price including tax in quote currency
     *
     * @return float|null
     */
    public function getPriceInclTax()
    {
        return $this->_get(self::PRICE_INCL_TAX);
    }

    /**
     * Get price including tax in base currency
     *
     * @return float|null
     */
    public function getBasePriceInclTax()
    {
        return $this->_get(self::BASE_PRICE_INCL_TAX);
    }

    /**
     * Get row total including tax in quote currency
     *
     * @return float|null
     */
    public function getRowTotalInclTax()
    {
        return $this->_get(self::ROW_TOTAL_INCL_TAX);
    }

    /**
     * Get row total including tax in base currency
     *
     * @return float|null
     */
    public function getBaseRowTotalInclTax()
    {
        return $this->_get(self::BASE_ROW_TOTAL_INCL_TAX);
    }
}
