<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * Interface CategoryManagementInterface must be implemented in new model \Magento\Catalog\Model\CategoryManagement
 */
interface CategoryManagementInterface
{
    /**
     * Update category
     *
     * @param int $categoryId category to be updated
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @see \Magento\Catalog\Service\V1\Category\WriteServiceInterface::update
     */
    public function update($categoryId, \Magento\Catalog\Api\Data\CategoryInterface $category);

    /**
     * Retrieve list of categories
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface containing Tree objects
     * @see \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface::tree
     */
    public function getTree();

    /**
     * Move category
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Category\WriteServiceInterface::move
     */
    public function move($categoryId, $parentId, $afterId = null);
}
