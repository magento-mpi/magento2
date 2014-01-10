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
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\Url
     */
    protected $viewUrl;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies
     * 
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\View\Url $viewUrl,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->viewUrl = $viewUrl;
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
        return $this->viewUrl->getViewFileUrl(self::DEFAULT_PREVIEW_IMAGE);
    }

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    public function getImagePreviewDirectory()
    {
        return $this->filesystem->getPath(\Magento\Filesystem::MEDIA) . '/' . self::PREVIEW_DIRECTORY_PATH;
    }

    /**
     * Temporary directory path to store images
     *
     * @return string
     */
    public function getTemporaryDirectory()
    {
        return $this->filesystem->getPath(\Magento\Filesystem::MEDIA) . '/theme/origin';
    }
}
