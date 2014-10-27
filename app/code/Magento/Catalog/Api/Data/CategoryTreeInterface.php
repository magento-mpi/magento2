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
interface CategoryTreeInterface extends \Magento\Catalog\Api\Data\CategoryInterface
{
    const CHILDREN_DATA = 'children';
    const PRODUCT_COUNT = 'product_count';

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
    public function getChildrenData();
}
