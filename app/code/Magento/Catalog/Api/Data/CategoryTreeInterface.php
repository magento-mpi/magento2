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
 * @todo create new model Catalog/Model/Category/Tree
 * @see \Magento\Catalog\Service\V1\Data\Eav\Category\Tree
 */
interface CategoryTreeInterface
{
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
     * @return \Magento\Catalog\Api\Data\Category\TreeInterface[]
     */
    public function getChildren();
}
