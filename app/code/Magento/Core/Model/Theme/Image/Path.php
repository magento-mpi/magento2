<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Theme\Image;

use Magento\View\Design\ThemeInterface;

/**
 * Theme Image Path
 */
class Path implements \Magento\View\Design\Theme\Image\PathInterface
{
    /**
     * Default theme preview image
     */
    const DEFAULT_PREVIEW_IMAGE = 'Magento_Core::theme/default_preview.jpg';

    /**
     * Media Directory
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\View\Url
     */
    protected $viewUrl;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies
     *
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFilesystem
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFilesystem,
        \Magento\View\Url $viewUrl,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::MEDIA_DIR);
        $this->viewUrl = $viewUrl;
        $this->viewFileSystem = $viewFilesystem;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get url to preview image
     *
     * @param \Magento\Core\Model\Theme|ThemeInterface $theme
     * @return string
     */
    public function getPreviewImageUrl(ThemeInterface $theme)
    {
        return $theme->isPhysical()
            ? $this->viewUrl->getViewFileUrl($theme->getPreviewImage(), [
                'area'       => $theme->getData('area'),
                'themeModel' => $theme
            ])
            : $this->_storeManager->getStore()->getBaseUrl(\Magento\UrlInterface::URL_TYPE_MEDIA)
                . self::PREVIEW_DIRECTORY_PATH . '/' . $theme->getPreviewImage();
    }

    /**
     * Get path to preview image
     *
     * @param \Magento\Core\Model\Theme|ThemeInterface $theme
     * @return string
     */
    public function getPreviewImagePath(ThemeInterface $theme)
    {
        return $theme->isPhysical()
            ? $this->viewFileSystem->getViewFile($theme->getPreviewImage(), [
                'area'       => $theme->getData('area'),
                'themeModel' => $theme
            ])
            : $this->mediaDirectory->getAbsolutePath(self::PREVIEW_DIRECTORY_PATH . '/' . $theme->getPreviewImage());
    }

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    public function getPreviewImageDefaultUrl()
    {
        return $this->viewUrl->getViewFileUrl(self::DEFAULT_PREVIEW_IMAGE);
    }

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    public function getImagePreviewDirectory()
    {
        return $this->mediaDirectory->getAbsolutePath(self::PREVIEW_DIRECTORY_PATH);
    }

    /**
     * Temporary directory path to store images
     *
     * @return string
     */
    public function getTemporaryDirectory()
    {
        return $this->mediaDirectory->getRelativePath('/theme/origin');
    }
}
