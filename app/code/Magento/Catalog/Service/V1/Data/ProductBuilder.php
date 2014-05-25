<?php

namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

class ProductBuilder extends \Magento\Framework\Service\Data\EAV\AbstractObjectBuilder
{
    /**
     * @var ProductMetadataServiceInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies.
     *
     * @param AttributeValueBuilder $valueBuilder
     * @param ProductMetadataServiceInterface $metadataService
     */
    public function __construct(
        AttributeValueBuilder $valueBuilder,
        ProductMetadataServiceInterface $metadataService
    ) {
        parent::__construct($valueBuilder);
        $this->metadataService = $metadataService;
    }

    /**
     * Template method used to configure the attribute codes for the product attributes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        $attributeCodes = array();
        foreach ($this->metadataService->getCustomAttributesMetadata() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }

    /**
     * Set id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Product::ID, $value);
    }

    /**
     * Set Sku
     *
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(Product::SKU, $value);
    }

    /**
     * Set store id
     *
     * @param int $value
     * @return $this
     */
    public function setStoreId($value)
    {
        return $this->_set(Product::STORE_ID, $value);
    }

    /**
     * Set price
     *
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->_set(Product::PRICE, $value);
    }

    /**
     * Set visibility
     *
     * @param int $value
     * @return $this
     */
    public function setVisibility($value)
    {
        return $this->_set(Product::VISIBILITY, $value);
    }

    /**
     * Set TypeId
     *
     * @param int $value
     * @return $this
     */
    public function setTypeId($value)
    {
        return $this->_set(Product::TYPE_ID, $value);
    }

    /**
     * Set created time
     *
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        return $this->_set(Product::CREATED_AT, $value);
    }

    /**
     * Set updated time
     *
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        return $this->_set(Product::UPDATED_AT, $value);
    }

    /**
     * Set status
     *
     * @param int $value
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->_set(Product::STATUS, $value);
    }

    /**
     * Set weight
     *
     * @param float $value
     * @return $this
     */
    public function setWeight($value)
    {
        return $this->_set(Product::WEIGHT, $value);
    }
}
