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
     * @var \Magento\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Initialize dependencies
     *
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\Repository $assetRepo
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Asset\Repository $assetRepo,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::MEDIA_DIR);
        $this->assetRepo = $assetRepo;
        $this->storeManager = $storeManager;
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
            ? $this->assetRepo->getUrlWithParams($theme->getPreviewImage(), [
                'area'       => $theme->getData('area'),
                'themeModel' => $theme
            ])
            : $this->storeManager->getStore()->getBaseUrl(\Magento\UrlInterface::URL_TYPE_MEDIA)
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
            ? $this->assetRepo->createAsset($theme->getPreviewImage(), [
                'area'       => $theme->getData('area'),
                'themeModel' => $theme
            ])->getSourceFile()
            : $this->mediaDirectory->getAbsolutePath(self::PREVIEW_DIRECTORY_PATH . '/' . $theme->getPreviewImage());
    }

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    public function getPreviewImageDefaultUrl()
    {
        return $this->assetRepo->getUrl(self::DEFAULT_PREVIEW_IMAGE);
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
