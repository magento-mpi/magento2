<?php
/**
 * Product Media Content
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

use \Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class GalleryEntryContent extends AbstractExtensibleObject
{
    const DATA = 'data';
    const MIME_TYPE = 'mime_type';
    const NAME = 'name';

    /**
     * Retrieve media data (base64 encoded content)
     *
     * @return string
     */
    public function getData()
    {
        return $this->_get(self::DATA);
    }

    /**
     * Retrieve MIME type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->_get(self::MIME_TYPE);
    }

    /**
     * Retrieve image name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }
}
