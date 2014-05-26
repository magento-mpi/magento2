<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;
use Magento\Catalog\Service\V1\Data\Product;

/**
 * Class ProductBuilder
 * @package Magento\Catalog\Service\V1\Data
 */
class ProductBuilder extends \Magento\Framework\Service\Data\EAV\AbstractObjectBuilder
{
    /**
     * @var ProductMetadataServiceInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Service\Data\Eav\AttributeValueBuilder $valueBuilde
     * @param ProductMetadataServiceInterface $metadataService
     */
    public function __construct(
        AttributeValueBuilder $valueBuilde
        //ProductMetadataServiceInterface $metadataService
    ) {
        parent::__construct($valueBuilde);
        //$this->metadataService = $metadataService;
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
     * @param int $value
     */
    public function setId($value)
    {
        return $this->_set(Product::ID, $value);
    }

    /**
     * @param string $value
     */
    public function setSku($value)
    {
        return $this->_set(Product::SKU, $value);
    }

    /**
     * @param int $value
     */
    public function setStoreId($value)
    {
        return $this->_set(Product::STORE_ID, $value);
    }

    /**
     * @param float $value
     */
    public function setPrice($value)
    {
        return $this->_set(Product::PRICE, $value);
    }

    /**
     * @param int $value
     */
    public function setVisibility($value)
    {
        return $this->_set(Product::VISIBILITY, $value);
    }

    /**
     * @param int $value
     */
    public function setTypeId($value)
    {
        return $this->_set(Product::TYPE_ID, $value);
    }

    /**
     * @param \DataTime $value
     */
    public function setCreatedAt($value)
    {
        return $this->_set(Product::CREATED_AT, $value);
    }

    /**
     * @param \DataTime $value
     */
    public function setUpdatedAt($value)
    {
        return $this->_set(Product::UPDATED_AT, $value);
    }

    /**
     * @param int $value
     */
    public function setStatus($value)
    {
        return $this->_set(Product::STATUS, $value);
    }
    
    
}
