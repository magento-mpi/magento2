<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Media library image config interface
 */
namespace Magento\Catalog\Model\Product\Media;

interface ConfigInterface
{
    /**
     * Retrieve base url for media files
     *
     * @return string
     */
    public function getBaseMediaUrl();

    /**
     * Retrieve base path for media files
     *
     * @return string
     */
    public function getBaseMediaPath();

    /**
     * Retrieve url for media file
     *
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file);

    /**
     * Retrieve file system path for media file
     *
     * @param string $file
     * @return string
     */
    public function getMediaPath($file);
}
