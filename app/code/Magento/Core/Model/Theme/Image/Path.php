<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Image Path
 */
namespace Magento\Core\Model\Theme\Image;

class Path
{
    /**
     * Image preview path
     */
    const PREVIEW_DIRECTORY_PATH = 'theme/preview';

    /**
     * Default theme preview image
     */
    const DEFAULT_PREVIEW_IMAGE = 'Magento_Core::theme/default_preview.jpg';

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Core\Model\Dir $dir
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Core\Model\Dir $dir,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\StoreManager $storeManager
    ) {
        $this->_dir = $dir;
        $this->_viewUrl = $viewUrl;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get preview image directory url
     *
     * @return string
     */
    public function getPreviewImageDirectoryUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_MEDIA)
            . self::PREVIEW_DIRECTORY_PATH . '/';
    }

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    public function getPreviewImageDefaultUrl()
    {
        return $this->_viewUrl->getViewFileUrl(self::DEFAULT_PREVIEW_IMAGE);
    }

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    public function getImagePreviewDirectory()
    {
        return $this->_dir->getDir(\Magento\Core\Model\Dir::MEDIA) . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, self::PREVIEW_DIRECTORY_PATH);
    }

    /**
     * Temporary directory path to store images
     *
     * @return string
     */
    public function getTemporaryDirectory()
    {
        return implode(DIRECTORY_SEPARATOR, array(
            $this->_dir->getDir(\Magento\Core\Model\Dir::MEDIA), 'theme', 'origin'
        ));
    }
}
