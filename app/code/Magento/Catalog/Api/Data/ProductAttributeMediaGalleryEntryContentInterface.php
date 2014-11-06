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

interface ProductAttributeMediaGalleryEntryContentInterface
{
    const DATA = 'data';
    const MIME_TYPE = 'mime_type';
    const NAME = 'name';

    /**
     * Retrieve media data (base64 encoded content)
     *
     * @return string
     */
    public function getEntryData();

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
