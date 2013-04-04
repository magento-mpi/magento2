<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Image Path
 */
class Mage_Core_Model_Theme_Image_Path
{
    /**
     * Image preview path
     */
    const PREVIEW_DIRECTORY_PATH = 'theme/preview';

    /**
     * Default theme preview image
     */
    const DEFAULT_PREVIEW_IMAGE = 'Mage_Core::theme/default_preview.jpg';

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_designPackage;

    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Dir $dir
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param Mage_Core_Model_StoreManager $storeManager
     */
    public function __construct(
        Mage_Core_Model_Dir $dir,
        Mage_Core_Model_Design_PackageInterface $designPackage,
        Mage_Core_Model_StoreManager $storeManager
    ) {
        $this->_dir = $dir;
        $this->_designPackage = $designPackage;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get preview image directory url
     *
     * @return string
     */
    public function getPreviewImageDirectoryUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
            . self::PREVIEW_DIRECTORY_PATH . '/';
    }

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    public function getPreviewImageDefaultUrl()
    {
        return $this->_designPackage->getViewFileUrl(self::DEFAULT_PREVIEW_IMAGE);
    }

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    public function getImagePreviewDirectory()
    {
        return $this->_dir->getDir(Mage_Core_Model_Dir::MEDIA) . DIRECTORY_SEPARATOR
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
            $this->_dir->getDir(Mage_Core_Model_Dir::MEDIA), 'theme', 'origin'
        ));
    }
}
