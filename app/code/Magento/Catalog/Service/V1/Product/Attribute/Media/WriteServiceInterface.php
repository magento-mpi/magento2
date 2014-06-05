<?php
/**
 * Product Media Attribute Write Service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry;
use \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryContent;

interface WriteServiceInterface
{
    /**
     * Create new gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry $entry
     * @param \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryContent $entryContent
     * @param int $storeId
     * @return int gallery entry ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function create($productSku, GalleryEntry $entry, GalleryEntryContent $entryContent, $storeId = null);

    /**
     * Update gallery entry
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry $entry
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function update($productSku, GalleryEntry $entry, $storeId = null);

    /**
     * Remove gallery entry
     *
     * @param string $productSku
     * @param int $entryId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete($productSku, $entryId);
}
