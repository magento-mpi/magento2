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

use \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryContentInterface as ContentInterface;
use \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryInterface;

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
     * @param \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryInterface $entry
     * @param \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryContentInterface $entryContent
     * @param int $storeId
     * @return int gallery entry ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function create($productSku, GalleryEntryInterface $entry, ContentInterface $entryContent, $storeId = 0);

    /**
     * Update gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryInterface $entry
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function update($productSku, GalleryEntryInterface $entry, $storeId = 0);

    /**
     * Remove gallery entry
     *
     * @param string $productSku
     * @param int $entryId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function remove($productSku, $entryId);

    /**
     * Return information about gallery entry
     *
     * @param string $productSku
     * @param int $imageId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryInterface
     */
    public function get($productSku, $imageId);

    /**
     * Retrieve the list of gallery entries associated with given product
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\Product\Attribute\Media\GalleryEntryInterface[]
     */
    public function getList($productSku);
}
