<?php
/**
 * Product Media Attribute Write Service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryContentInterface as ContentInterface;
use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;

/**
 * @todo implement this interface as a \Magento\Catalog\Model\Product\Attribute\Media\GalleryManagement.
 * Move logic from service there.
 */
interface ProductAttributeMediaGalleryManagementInterface
{
    /**
     * Create new gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryContentInterface $entryContent
     * @param int $storeId
     * @return int gallery entry ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Media\WriteServiceInterface::create
     */
    public function create($productSku, ProductAttributeMediaGalleryEntryInterface $entry, ContentInterface $entryContent, $storeId = 0);

    /**
     * Update gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Media\WriteServiceInterface::update
     */
    public function update($productSku, ProductAttributeMediaGalleryEntryInterface $entry, $storeId = 0);

    /**
     * Remove gallery entry
     *
     * @param string $productSku
     * @param int $entryId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Media\WriteServiceInterface::delete
     */
    public function remove($productSku, $entryId);

    /**
     * Return information about gallery entry
     *
     * @param string $productSku
     * @param int $imageId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Media\ReadServiceInterface::info
     */
    public function get($productSku, $imageId);

    /**
     * Retrieve the list of gallery entries associated with given product
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface[]
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Media\ReadServiceInterface::getList
     */
    public function getList($productSku);
}
