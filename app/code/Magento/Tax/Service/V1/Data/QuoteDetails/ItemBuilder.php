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
     * @return $this
     */
    public function setCode()
    {
        return $this->_set(Item::KEY_CODE);
    }

    /**
     * Set type (e.g., shipping, product, wee, gift wrapping, etc.)
     *
     * @return $this
     */
    public function setType()
    {
        return $this->_set(Item::KEY_TYPE);
    }

    /**
     * Set tax class id
     *
     * @return $this
     */
    public function setTaxClassId()
    {
        return $this->_set(Item::KEY_TAX_CLASS_ID);
    }

    /**
     * Set row total
     *
     * @return $this
     */
    public function setRowTotal()
    {
        return $this->_set(Item::KEY_ROW_TOTAL);
    }

    /**
     * Set unit price
     *
     * @return $this
     */
    public function setUnitPrice()
    {
        return $this->_set(Item::KEY_UNIT_PRICE);
    }

    /**
     * Set quantity
     *
     * @return $this
     */
    public function setQty()
    {
        return $this->_set(Item::KEY_QTY);
    }

    /**
     * Set indicate that if the tax is included in the unit price and row total
     *
     * @return $this
     */
    public function setTaxIncluded()
    {
        return $this->_set(Item::KEY_TAX_INCLUDED);
    }

    /**
     * Set short description
     *
     * @return $this
     */
    public function setShortDescription()
    {
        return $this->_set(Item::KEY_SHORT_DESCRIPTION);
    }

    /**
     * Set discount amount
     *
     * @return $this
     */
    public function setDiscountAmount()
    {
        return $this->_set(Item::KEY_DISCOUNT_AMOUNT);
    }

    /**
     * Set related code
     *
     * @return $this
     */
    public function setRelatedCode()
    {
        return $this->_set(Item::KEY_RELATED_CODE);
    }
}
