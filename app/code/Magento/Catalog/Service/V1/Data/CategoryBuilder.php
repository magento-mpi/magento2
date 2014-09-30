<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;
use Magento\Framework\Service\Data\AttributeValueBuilder;

/**
 * @codeCoverageIgnore
 */
class CategoryBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param \Magento\Catalog\Service\V1\Category\MetadataServiceInterface $metadataService
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        \Magento\Catalog\Service\V1\Category\MetadataServiceInterface $metadataService
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
    }

    /**
     * Set category id
     *
     * @param  int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Category::ID, $value);
    }

    /**
     * Set category parent id
     *
     * @param  int $value
     * @return $this
     */
    public function setParentId($value)
    {
        return $this->_set(Category::PARENT_ID, $value);
    }

    /**
     * Set path of the category
     *
     * @param  string $value
     * @return $this
     */
    public function setPath($value)
    {
        return $this->_set(Category::PATH, $value);
    }

    /**
     * Set position of the category
     *
     * @param  int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Category::POSITION, $value);
    }

    /**
     * Set category level
     *
     * @param  int $value
     * @return $this
     */
    public function setLevel($value)
    {
        return $this->_set(Category::LEVEL, $value);
    }

    /**
     * Set category children count
     *
     * @param  int $value
     * @return $this
     */
    public function setChildrenCount($value)
    {
        return $this->_set(Category::CHILDREN_COUNT, $value);
    }

    /**
     * Name of the created category
     *
     * @param  string $value
     * @return $this
     */
    public function setName($value)
    {
        return $this->_set(Category::NAME, $value);
    }

    /**
     * Set whether the category will be visible in the frontend
     *
     * @param  bool $value
     * @return $this
     */
    public function setActive($value)
    {
        return $this->_set(Category::ACTIVE, $value);
    }
}
