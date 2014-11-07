<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class LinkBuilder extends ExtensibleObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(Link::SKU, $value);
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setQty($value)
    {
        return $this->_set(Link::QTY, $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Link::POSITION, $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setOptionId($value)
    {
        return $this->_set(Link::OPTION_ID, $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setDefined($value)
    {
        return $this->_set(Link::DEFINED, (bool)$value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setDefault($value)
    {
        return $this->_set(Link::IS_DEFAULT, (bool)$value);
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->_set(Link::PRICE, $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPriceType($value)
    {
        return $this->_set(Link::PRICE_TYPE, $value);
    }

    /**
     * Set can change quantity
     *
     * @param int $canChangeQuantity
     * @return $this
     */
    public function setCanChangeQuantity($canChangeQuantity)
    {
        return $this->_set(Link::CAN_CHANGE_QUANTITY, $canChangeQuantity);
    }
}
