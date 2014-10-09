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
 * Crete new model \Magento\Catalog\Model\CategoryLinkManagement implements \Magento\Catalog\Api\CategoryLinkManagement
 */
interface CategoryLinkManagement
{
    /**
     * Get assigned product links to the category
     *
     * @param int $categoryId
     * @return Data\Category\ProductLinkInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\ReadServiceInterface::assignedProducts - previous interface
     */
    public function getList($categoryId);

    /**
     * Assign a product to the required category
     *
     * @param int $categoryId
     * @param Data\Category\ProductLinkInterface $productLink
     * @return bool will returned True if assigned
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::assignProduct - previous interface
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::updateProduct - previous interface
     */
    public function save($categoryId, Data\Category\ProductLinkInterface $productLink);

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
