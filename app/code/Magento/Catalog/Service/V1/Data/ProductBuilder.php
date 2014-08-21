<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\Product\MetadataServiceInterface;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

/**
 * @codeCoverageIgnore
 */
class ProductBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService
    ) {
        parent::__construct($objectFactory, $valueBuilder);
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
     * Set Sku
     *
     * @param string|null $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(Product::SKU, $value);
    }

    /**
     * Set Name
     *
     * @param string|null $value
     * @return $this
     */
    public function setName($value)
    {
        return $this->_set(Product::NAME, $value);
    }

    /**
     * Set store id
     *
     * @param int|null $value
     * @return $this
     */
    public function setStoreId($value)
    {
        return $this->_set(Product::STORE_ID, $value);
    }

    /**
     * Set price
     *
     * @param float|null $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->_set(Product::PRICE, $value);
    }

    /**
     * Set visibility
     *
     * @param int|null $value
     * @return $this
     */
    public function setVisibility($value)
    {
        return $this->_set(Product::VISIBILITY, $value);
    }

    /**
     * Set TypeId
     *
     * @param int|null $value
     * @return $this
     */
    public function setTypeId($value)
    {
        return $this->_set(Product::TYPE_ID, $value);
    }

    /**
     * Set created time
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string|null $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        throw new \Magento\Framework\Exception\InputException(
            'Field "created_at" is readonly',
            ['fieldName' => 'created_at']
        );
    }

    /**
     * Set updated time
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string|null $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        throw new \Magento\Framework\Exception\InputException(
            'Field "updated_at" is readonly',
            ['fieldName' => 'updated_at']
        );
    }

    /**
     * Set status
     *
     * @param int|null $value
     * @return $this
     */
    public function setAttributeSetId($value)
    {
        return $this->_set(Product::ATTRIBUTE_SET_ID, $value);
    }

    /**
     * Set status
     *
     * @param int|null $value
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->_set(Product::STATUS, $value);
    }

    /**
     * Set weight
     *
     * @param float|null $value
     * @return $this
     */
    public function setWeight($value)
    {
        return $this->_set(Product::WEIGHT, $value);
    }
}
