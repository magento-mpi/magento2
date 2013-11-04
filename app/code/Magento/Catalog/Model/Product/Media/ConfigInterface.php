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
     * Retrive base url for media files
     *
     * @return string
     */
    function getBaseMediaUrl();

    /**
     * Retrive base path for media files
     *
     * @return string
     */
    function getBaseMediaPath();

    /**
     * Retrive url for media file
     *
     * @param string $file
     * @return string
     */
    function getMediaUrl($file);

    /**
     * Retrive file system path for media file
     *
     * @param string $file
     * @return string
     */
    function getMediaPath($file);
}
