<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Framework\Service\Data\Eav\AbstractObjectBuilder;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;
use Magento\Catalog\Service\V1\Category\MetadataServiceInterface;

class CategoryBuilder extends AbstractObjectBuilder
{
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
