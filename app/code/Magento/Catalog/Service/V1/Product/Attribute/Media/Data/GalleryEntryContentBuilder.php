<?php
/**
 * Product Media Content Builder
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class GalleryEntryContentBuilder extends ExtensibleObjectBuilder
{
    /**
     * Set media data (base64 encoded content)
     *
     * @param string $data
     * @return $this
     */
    public function setData($data)
    {
        return $this->_set(GalleryEntryContent::DATA, $data);
    }

    /**
     * Set MIME type
     *
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        return $this->_set(GalleryEntryContent::MIME_TYPE, $mimeType);
    }

    /**
     * Set image name (without extension)
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->_set(GalleryEntryContent::NAME, $name);
    }
}
