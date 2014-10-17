<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface CategoryLinkRepositoryInterface
{
    /**
     * Assign a product to the required category
     *
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
    public function save(\Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink);

    /**
     * Remove the product assignment from the category
     *
     * @param \Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink
     * @return bool will returned True if products successfully deleted
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::removeProduct - previous interface
     */
    public function delete(\Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink);
}
