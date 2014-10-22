<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * @see \Magento\Catalog\Service\V1\Data\Eav\Category\Tree
 */
interface CategoryTreeInterface extends \Magento\Framework\Api\Data\ExtensibleObjectInterface
{
    const ID = 'id';
    const PARENT_ID = 'parent_id';
    const NAME = 'name';
    const ACTIVE = 'active';
    const POSITION = 'position';
    const LEVEL = 'level';
    const CHILDREN = 'children';
    const PRODUCT_COUNT = 'product_count';

    public function getId();
    /**
     * Get parent category ID
     *
     * @return int
     */
    public function getParentId();

    /**
     * Get category name
     *
     * @return string
     */
    public function getName();

    /**
     * Check whether category is active
     *
     * @return bool
     */
    public function isActive();

    /**
     * Get category position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Get category level
     *
     * @return int
     */
    public function getLevel();

    /**
     * Get product count
     *
     * @return int
     */
    public function getProductCount();

    /**
     * Get category level
     *
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface[]
     */
    public function getChildren();
}
