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
interface CategoryDetailsInterface extends \Magento\Catalog\Api\Data\CategoryInterface
{

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const URL_KEY = 'url_key';
    const PATH = 'path';
    const DISPLAY_MODE = 'display_mode';
    const AVAILABLE_SORT_BY = 'available_sort_by';
    const INCLUDE_IN_MENU = 'include_in_menu';

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
