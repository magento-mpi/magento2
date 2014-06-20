<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category\Info;

use Magento\Catalog\Service\V1\Category\MetadataServiceInterface;
use Magento\Framework\Service\Data\Eav\AbstractObjectBuilder;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;
use Magento\Framework\Service\Data\ObjectFactory;

class MetadataBuilder extends AbstractObjectBuilder
{
    /**
     * @param ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService
    ) {
        parent::__construct($objectFactory, $valueBuilder);
        $this->metadataService = $metadataService;
    }

    /**
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        $attributeCodes = array();
        foreach ($this->metadataService->getCustomAttributesMetadata() as $attribute) {
            /** @var \Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata @attribute */
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setCategoryId($value)
    {
        $this->_set(Metadata::ID, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        $this->_set(Metadata::POSITION, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setLevel($value)
    {
        $this->_set(Metadata::LEVEL, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setParentId($value)
    {
        $this->_set(Metadata::PARENT_ID, $value);
        return $this;
    }

    /**
     * @param int[] $value
     * @return $this
     */
    public function setChildren($value)
    {
        $this->_set(Metadata::CHILDREN, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->_set(Metadata::CREATED_AT, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->_set(Metadata::UPDATED_AT, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->_set(Metadata::NAME, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUrlKey($value)
    {
        $this->_set(Metadata::URL_KEY, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPath($value)
    {
        $this->_set(Metadata::PATH, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDisplayMode($value)
    {
        $this->_set(Metadata::DISPLAY_MODE, $value);
        return $this;
    }

    /**
     * @param string[] $value
     * @return $this
     */
    public function setAvailableSortBy($value)
    {
        $this->_set(Metadata::AVAILABLE_SORT_BY, $value);
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setIncludeInMenu($value)
    {
        $this->_set(Metadata::INCLUDE_IN_MENU, $value);
        return $this;
    }
}
