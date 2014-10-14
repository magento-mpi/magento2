<?php
/**
 * Product Media Content
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * @todo implement this interface as \Magento\Catalog\Model\Product\Attribute\Media\GalleryEntryContent.
 * Move logic from service there. Move corresponding helper classes too.
 */
interface ProductAttributeMediaGalleryEntryContentInterface
{
    /**
     * Retrieve media data (base64 encoded content)
     *
     * @return string
     */
    public function getData();

    /**
     * Retrieve MIME type
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Retrieve image name
     *
     * @return string
     */
    public function getName();
}
