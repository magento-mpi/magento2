<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme data helper
 */
class Mage_Theme_Helper_Storage extends Mage_Core_Helper_Abstract
{
    /**
     * Current directory path
     *
     * @var string
     */
    protected $_currentPath;

    /**
     * Magento filesystem
     *
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Constructor
     *
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Magento_Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->getStorageRoot());
    }

    /**
     * Get wysiwyg helper
     *
     * @return Mage_Cms_Helper_Wysiwyg_Images
     */
    protected function _getWysiwygHelper()
    {
        return Mage::getObjectManager()->get('Mage_Cms_Helper_Wysiwyg_Images');
    }

    /**
     * Convert path to id
     *
     * @param string $path
     * @return string
     */
    public function convertPathToId($path)
    {
        $path = str_replace($this->getStorageRoot(), '', $path);
        return $this->_getWysiwygHelper()->idEncode($path);
    }

    /**
     * Convert id to path
     *
     * @param string $id
     * @return string
     */
    public function convertIdToPath($id)
    {
        $path = $this->_getWysiwygHelper()->idDecode($id);
        if (!strstr($path, $this->getStorageRoot())) {
            $path = $this->getStorageRoot() . $path;
        }
        return $path;
    }

    /**
     * Get short file name
     *
     * @param string $fileName
     * @return string
     */
    public function getShortFilename($fileName)
    {
        return $this->_getWysiwygHelper()->getShortFilename($fileName);
    }

    /**
     * Get storage root directory
     *
     * @return string
     */
    public function getStorageRoot()
    {
        $storageRoot = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'storage'
            . DIRECTORY_SEPARATOR . $this->_getStorageType();

        if (!is_dir($storageRoot)) {
            /** create folder */
        }

        return $storageRoot;
    }

    /**
     * Get storage type
     *
     * @return mixed
     */
    protected function _getStorageType()
    {
        return Mage::registry('storage_file_type');
    }

    /**
     * Id decode
     *
     * @param string $string
     * @return string
     */
    public function idDecode($string)
    {
        return $this->_getWysiwygHelper()->idDecode($string);
    }

    /**
     * Id encode
     *
     * @param string $string
     * @return string
     */
    public function idEncode($string)
    {
        return $this->_getWysiwygHelper()->idEncode($string);
    }

    /**
     * Get curent path
     *
     * @return string
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->getStorageRoot();
            $path = $this->_getRequest()->getParam($this->_getWysiwygHelper()->getTreeNodeName());
            if ($path) {
                $path = $this->convertIdToPath($path);
                if (is_dir($path)) {
                    $currentPath = $path;
                }
            }
            $this->_currentPath = $currentPath;
        }
        return $this->_currentPath;
    }

    /**
     * Get allowed extensions by type
     *
     * @return array
     */
    public function getAllowedExtensionsByType()
    {
        switch ($this->_getStorageType()) {
            case Mage_Theme_Model_Wysiwyg_Storage::TYPE_FONT:
                $extensions = array('ttf', 'otf', 'eot', 'svg', 'woff');
                break;
            case Mage_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE:
                $extensions = array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp');
                break;
            default:
                Mage::throwException($this->__('Invalid type'));
        }

        return $extensions;
    }

    /**
     * Get session model
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getSession()
    {
        return Mage::getSingleton('Mage_Backend_Model_Session');
    }

}
