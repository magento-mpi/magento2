<?php
/**
 * Product Media Gallery Entry Resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use \Magento\Catalog\Model\Product;

class GalleryEntryResolver
{
    /**
     * Retrieve file path that corresponds to the given gallery entry ID
     *
     * @param Product $product
     * @param int $entryId
     * @return string|null
     */
    public function getEntryFilePathById(Product $product, $entryId)
    {
        $mediaGalleryData = $product->getData('media_gallery');
        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return null;
        }

        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['value_id'] == $entryId) {
                return $image['file'];
            }
        }
        return null;
    }

    /**
     * Retrieve gallery entry ID that corresponds to the given file path
     *
     * @param Product $product
     * @param string $filePath
     * @return int|null
     */
    public function getEntryIdByFilePath(Product $product, $filePath)
    {
        $mediaGalleryData = $product->getData('media_gallery');
        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return null;
        }

        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['file'] == $filePath) {
                return isset($image['value_id']) ? $image['value_id'] : null;
            }
        }
        return null;
    }
}
