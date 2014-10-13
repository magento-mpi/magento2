<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Category\ProductLinks\ReadServiceInterface
 * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface
 *
 * Crete new model \Magento\Catalog\Model\CategoryLinkManagement implements \Magento\Catalog\Api\CategoryLinkManagementInterface
 */
interface CategoryLinkManagementInterface
{
    /**
     * Get assigned product links to the category
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryProductLinkInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\ReadServiceInterface::assignedProducts - previous interface
     */
    public function getList($categoryId);

    /**
     * Assign a product to the required category
     *
     * @param int $categoryId
     * @param \Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink
     * @return bool will returned True if assigned
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::assignProduct - previous interface
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::updateProduct - previous interface
     */
    public function save($categoryId, \Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink);

    /**
     * Remove the product assignment from the category
     *
     * @param int $categoryId
     * @param string $productSku Product SKU
     * @return bool will returned True if products successfully deleted
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::removeProduct - previous interface
     */
    public function remove($categoryId, $productSku);
}
