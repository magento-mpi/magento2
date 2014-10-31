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

/**
 * Created from @see \Magento\Catalog\Service\V1\Data\Category
 */
interface CategoryInterface extends \Magento\Framework\Api\Data\ExtensibleDataInterface
{
    /**
     * @return int|null
     */
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
    public function getIsActive();

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
     * @return array|null
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
