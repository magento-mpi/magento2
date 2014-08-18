<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

use Magento\Catalog\Service\V1\Data\Category\ProductLink;

interface WriteServiceInterface
{
    /**
     * Assign a product to the required category
     *
     * @param int $categoryId
     * @param \Magento\Catalog\Service\V1\Data\Category\ProductLink $productLink
     * @return bool Will returned True if assigned
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function assignProduct($categoryId, ProductLink $productLink);

    /**
     * @param int $categoryId
     * @param \Magento\Catalog\Service\V1\Data\Category\ProductLink $productLink
     * @return bool Will returned True if updated
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function updateProduct($categoryId, ProductLink $productLink);

    /**
     * Remove the product assignment from the category.
     *
     * @param int $categoryId
     * @param string $productSku Product SKU
     * @return bool Will returned True if products sucessfully deleted
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function removeProduct($categoryId, $productSku);
}
