<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Image Path
 */
namespace Magento\Core\Model\Theme\Image;

class Path implements \Magento\View\Design\Theme\Image\PathInterface
{
    /**
     * Default theme preview image
     */
    const DEFAULT_PREVIEW_IMAGE = 'Magento_Core::theme/default_preview.jpg';

    /**
     * Filesystem instance
     *
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\Asset\Service
     */
    protected $assetService;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies
     * 
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\Service $assetService
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Asset\Service $assetService,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->assetService = $assetService;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get preview image directory url
     *
     * @return string
     */
    public function getPreviewImageDirectoryUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\UrlInterface::URL_TYPE_MEDIA)
            . self::PREVIEW_DIRECTORY_PATH . '/';
    }

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    public function getPreviewImageDefaultUrl()
    {
        return $this->assetService->getAssetUrl(self::DEFAULT_PREVIEW_IMAGE);
    }

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    public function getImagePreviewDirectory()
    {
        return $this->filesystem->getPath(\Magento\App\Filesystem::MEDIA_DIR) . '/' . self::PREVIEW_DIRECTORY_PATH;
    }

    /**
     * Temporary directory path to store images
     *
     * @return string
     */
    public function getTemporaryDirectory()
    {
        return $this->filesystem->getPath(\Magento\App\Filesystem::MEDIA_DIR) . '/theme/origin';
    }
}
