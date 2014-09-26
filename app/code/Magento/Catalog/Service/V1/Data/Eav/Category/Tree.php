<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category;

/**
 * @codeCoverageIgnore
 */
class Tree extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    const ID = 'id';
    const PARENT_ID = 'parent_id';
    const NAME = 'name';
    const ACTIVE = 'active';
    const POSITION = 'position';
    const LEVEL = 'level';
    const CHILDREN = 'children';
    const PRODUCT_COUNT = 'product_count';

    /**
     * Get category ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get parent category ID
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Get category name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Check whether category is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->_get(self::ACTIVE);
    }

    /**
     * Get category position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Get category level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->_get(self::LEVEL);
    }

    /**
     * Get product count
     *
     * @return int
     */
    public function getProductCount()
    {
        return $this->_get(self::PRODUCT_COUNT);
    }

    /**
     * Get category level
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\Category\Tree[]
     */
    public function getChildren()
    {
        return $this->_get(self::CHILDREN);
    }
}
