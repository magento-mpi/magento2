<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product\Link\Data;

use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

/**
 * Builder for the ProductLink Service Data Object
 *
 * @method ProductLink create()
 */
class ProductLinkBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    /**
     * @var array
     */
    protected $customAttributes = [];

    /**
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param array $customAttributesCodes
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        array $customAttributesCodes = array()
    ) {
        $this->customAttributes = $customAttributesCodes;
        parent::__construct($objectFactory, $valueBuilder);
    }

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->_set(ProductLink::SKU, $sku);
    }

    /**
     * Set product position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(ProductLink::POSITION, $position);
    }

    /**
     * Get custom attributes codes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        return array_merge(parent::getCustomAttributesCodes(), $this->customAttributes);
    }

    /**
     * Set parent product sku
     *
     * @param string $parentSku
     * @return $this
     */
    public function setParentSku($parentSku)
    {
        return $this->_set(ProductLink::PARENT_SKU, $parentSku);
    }

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        return $this->_set(ProductLink::OPTION_ID, $optionId);
    }

    /**
     * Set is default
     *
     * @param boolean $default
     * @return $this
     */
    public function setIsDefault($default)
    {
        return $this->_set(ProductLink::IS_DEFAULT, $default);
    }

    /**
     * Set price type
     *
     * @param int $priceType
     * @return $this
     */
    public function setPriceType($priceType)
    {
        return $this->_set(ProductLink::PRICE_TYPE, $priceType);
    }

    /**
     * Set price value
     *
     * @param float $priceValue
     * @return $this
     */
    public function setPriceValue($priceValue)
    {
        return $this->_set(ProductLink::PRICE_VALUE, $priceValue);
    }

    /**
     * Set quantity
     *
     * @param int $priceValue
     * @return $this
     */
    public function setQuantity($quantity)
    {
        return $this->_set(ProductLink::QUANTITY, $quantity);
    }

    /**
     * Set can change quantity
     *
     * @param int $canChangeQuantity
     * @return $this
     */
    public function setCanChangeQuantity($canChangeQuantity)
    {
        return $this->_set(ProductLink::CAN_CHANGE_QUANTITY, $canChangeQuantity);
    }
}
