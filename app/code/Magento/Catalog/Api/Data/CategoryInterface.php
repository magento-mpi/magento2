<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;


interface CategoryInterface extends \Magento\Framework\Api\Data\ExtensibleDataInterface
{
    const CATEGORY_ID = 'category_id';
    const PARENT_ID = 'parent_id';
    const NAME = 'name';
    const ACTIVE = 'active';
    const POSITION = 'position';
    const LEVEL = 'level';

    /**
     * @return int|null
     */
    public function getCategoryId();

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
}
