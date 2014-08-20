<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart\Totals;

/**
 * Cart Item Totals Builder
 *
 * @codeCoverageIgnore
 */
class ItemBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set item price in quote currency
     *
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->_set(Item::PRICE, $value);
    }

    /**
     * Set item price in base currency
     *
     * @param float $value
     * @return $this
     */
    public function setBasePrice($value)
    {
        return $this->_set(Item::BASE_PRICE, $value);
    }

    /**
     * Set items qty
     *
     * @param int $value
     * @return $this
     */
    public function setQty($value)
    {
        return $this->_set(Item::QTY, $value);
    }

    /**
     * Set row total in quote currency
     *
     * @param float $value
     * @return $this
     */
    public function setRowTotal($value)
    {
        return $this->_set(Item::ROW_TOTAL, $value);
    }

    /**
     * Set row total in base currency
     *
     * @param float $value
     * @return $this
     */
    public function setBaseRowTotal($value)
    {
        return $this->_set(Item::BASE_ROW_TOTAL, $value);
    }

    /**
     * Set row total with discount in quote currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setRowTotalWithDiscount($value)
    {
        return $this->_set(Item::ROW_TOTAL_WITH_DISCOUNT, $value);
    }

    /**
     * Set tax amount in quote currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setTaxAmount($value)
    {
        return $this->_set(Item::TAX_AMOUNT, $value);
    }

    /**
     * Set tax amount in base currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setBaseTaxAmount($value)
    {
        return $this->_set(Item::BASE_TAX_AMOUNT, $value);
    }

    /**
     * Set tax percent
     *
     * @param int|null $value
     * @return $this
     */
    public function setTaxPercent($value)
    {
        return $this->_set(Item::TAX_PERCENT, $value);
    }

    /**
     * Set discount amount in quote currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setDiscountAmount($value)
    {
        return $this->_set(Item::DISCOUNT_AMOUNT, $value);
    }

    /**
     * Set discount amount in base currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setBaseDiscountAmount($value)
    {
        return $this->_set(Item::BASE_DISCOUNT_AMOUNT, $value);
    }

    /**
     * Set discount percent
     *
     * @param int|null $value
     * @return $this
     */
    public function setDiscountPercent($value)
    {
        return $this->_set(Item::DISCOUNT_PERCENT, $value);
    }

    /**
     * Set price including tax in quote currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setPriceIncludingTax($value)
    {
        return $this->_set(Item::PRICE_INCL_TAX, $value);
    }

    /**
     * Set price including tax in base currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setBasePriceIncludingTax($value)
    {
        return $this->_set(Item::BASE_PRICE_INCL_TAX, $value);
    }

    /**
     * Set row total including tax in quote currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setRowTotalIncludingTax($value)
    {
        return $this->_set(Item::ROW_TOTAL_INCL_TAX, $value);
    }

    /**
     * Set row total including tax in base currency
     *
     * @param float|null $value
     * @return $this
     */
    public function setBaseRowTotalIncludingTax($value)
    {
        return $this->_set(Item::BASE_ROW_TOTAL_INCL_TAX, $value);
    }
}
