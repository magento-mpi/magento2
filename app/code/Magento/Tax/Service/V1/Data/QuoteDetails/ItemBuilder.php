<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\QuoteDetails;

/**
 * Builder for the Item Service Data Object
 *
 * @method Item create()
 */
class ItemBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set code (sku or shipping code)
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(Item::KEY_CODE, $code);
    }

    /**
     * Set type (e.g., shipping, product, wee, gift wrapping, etc.)
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(Item::KEY_TYPE, $type);
    }

    /**
     * Set tax class id
     *
     * @param int $taxClassId
     * @return $this
     */
    public function setTaxClassId($taxClassId)
    {
        return $this->_set(Item::KEY_TAX_CLASS_ID, $taxClassId);
    }

    /**
     * Set row total
     *
     * @param float $rowTotal
     * @return $this
     */
    public function setRowTotal($rowTotal)
    {
        return $this->_set(Item::KEY_ROW_TOTAL, $rowTotal);
    }

    /**
     * Set unit price
     *
     * @param float $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice)
    {
        return $this->_set(Item::KEY_UNIT_PRICE, $unitPrice);
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        return $this->_set(Item::KEY_QUANTITY, $quantity);
    }

    /**
     * Set indicate that if the tax is included in the unit price and row total
     *
     * @param bool $taxIncluded
     * @return $this
     */
    public function setTaxIncluded($taxIncluded)
    {
        return $this->_set(Item::KEY_TAX_INCLUDED, $taxIncluded);
    }

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription($shortDescription)
    {
        return $this->_set(Item::KEY_SHORT_DESCRIPTION, $shortDescription);
    }

    /**
     * Set discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount)
    {
        return $this->_set(Item::KEY_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * Set related code
     *
     * @param string $code
     * @return $this
     */
    public function setRelatedCode($code)
    {
        return $this->_set(Item::KEY_RELATED_CODE, $code);
    }
}
