<?php
/**
 * Category data interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface CategoryInterface
{
    /**
     * Category id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Category parent id
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Path of the category
     *
     * @return string|null
     */
    public function getPath();

    /**
     * Position of the category
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Category level
     *
     * @return int|null
     */
    public function getLevel();

    /**
     * Category children count
     *
     * @return int|null
     */
    public function getChildrenCount();
    /**
     * Category created date
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Category updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Name of the created category
     *
     * @return string|null
     */
    public function getName();

    /**
     * Defines whether the category will be visible in the frontend
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Get category level
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface[]|null
     */
    public function getChildren();

    /**
     * Get product count
     *
     * @return int|null
     */
    public function getProductCount();

}
