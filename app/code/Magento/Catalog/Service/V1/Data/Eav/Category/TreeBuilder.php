<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class TreeBuilder extends ExtensibleObjectBuilder
{
    /**
     * Set category ID
     *
     * @param int $categoryId
     * @return $this
     */
    public function setId($categoryId)
    {
        return $this->_set(Tree::ID, $categoryId);
    }

    /**
     * Set parent category ID
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->_set(Tree::PARENT_ID, $parentId);
    }

    /**
     * Set category name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->_set(Tree::NAME, $name);
    }

    /**
     * Set whether category is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setActive($isActive)
    {
        return $this->_set(Tree::ACTIVE, $isActive);
    }

    /**
     * Set category position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(Tree::POSITION, $position);
    }

    /**
     * Set product count
     *
     * @param int $productCount
     * @return int
     */
    public function setProductCount($productCount)
    {
        return $this->_set(Tree::PRODUCT_COUNT, $productCount);
    }

    /**
     * Set category level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        return $this->_set(Tree::LEVEL, $level);
    }

    /**
     * Set category level
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\Tree[] $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        return $this->_set(Tree::CHILDREN, $children);
    }
}
