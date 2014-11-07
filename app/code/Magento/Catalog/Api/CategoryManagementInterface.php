<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface CategoryManagementInterface
{
    /**
     * Retrieve list of categories
     *
     * @param int|null $rootCategoryId
     * @param int|null $depth
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface containing Tree objects
     */
    public function getTree($rootCategoryId = null, $depth = null);

    /**
     * Move category
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function move($categoryId, $parentId, $afterId = null);
}
