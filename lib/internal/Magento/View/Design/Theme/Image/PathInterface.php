<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Image;

use Magento\View\Design\ThemeInterface;

/**
 * Theme Image Path interface
 */
interface PathInterface
{
    /**
     * Image preview path
     */
    const PREVIEW_DIRECTORY_PATH = 'theme/preview';

    /**
     * Get preview image directory url
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return string
     */
    public function getPreviewImageUrl(ThemeInterface $theme);

    /**
     * Get path to preview image
     *
     * @param \Magento\Core\Model\Theme|ThemeInterface $theme
     * @return string
     */
    public function getPreviewImagePath(ThemeInterface $theme);

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    public function getPreviewImageDefaultUrl();

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    public function getImagePreviewDirectory();

    /**
     * Temporary directory path to store images
     *
     * @return string
     */
    public function getTemporaryDirectory();
}
