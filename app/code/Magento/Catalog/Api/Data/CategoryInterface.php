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

interface CategoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * Get parent category ID
     *
     * @return int|null
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
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Get category position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Get category level
     *
     * @return int|null
     */
    public function getLevel();

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface[]|null
     */
    public function getChildren();

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @return string|null
     */
    public function getUrlKey();

    /**
     * @return string|null
     */
    public function getPath();

    /**
     * @return string|null
     */
    public function getDisplayMode();

    /**
     * @return string[]|null
     */
    public function getAvailableSortBy();
    /**
     * @return bool|null
     */
    public function getIncludeInMenu();
}
