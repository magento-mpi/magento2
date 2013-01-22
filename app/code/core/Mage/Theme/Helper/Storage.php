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
     * Parameter name of node
     */
    const PARAM_NAME_NODE = 'node';

    /**
     * Parameter name of content type
     */
    const PARAM_NAME_CONTENT_TYPE = 'content_type';

    /**
     * Parameter name of theme identification number
     */
    const PARAM_NAME_THEME_ID = 'theme_id';

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
     * @var Mage_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * Constructor
     *
     * @param Magento_Filesystem $filesystem
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Backend_Model_Session $session,
        Mage_Core_Model_Theme_Factory $themeFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_session = $session;
        $this->_themeFactory = $themeFactory;

        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->getStorageRoot());
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
        return $this->urlEncode($path);
    }

    /**
     * Convert id to path
     *
     * @param string $id
     * @return string
     */
    public function convertIdToPath($id)
    {
        $path = $this->urlDecode($id);
        if (!strstr($path, $this->getStorageRoot())) {
            $path = $this->getStorageRoot() . $path;
        }
        return $path;
    }

    /**
     * Get short file name
     *
     * @param string $filename
     * @param int $maxLength
     * @return string
     */
    public function getShortFilename($filename, $maxLength = 20)
    {
        return strlen($filename) <= $maxLength ? $filename : substr($filename, 0, $maxLength) . '...';
    }

    /**
     * Get storage root directory
     *
     * @return string
     */
    public function getStorageRoot()
    {
        $storageRoot = $this->_getTheme()->getCustomizationPath() . DIRECTORY_SEPARATOR . $this->_getStorageType();
        return $storageRoot;
    }

    /**
     * Get theme module for custom static files
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _getTheme()
    {
        $themeId = $this->_getRequest()->getParam(self::PARAM_NAME_THEME_ID);
        $theme = $this->_themeFactory->create();
        if ($themeId && !$theme->load($themeId)->getId()) {
            Mage::throwException($this->__('Theme was not found.'));
        }
        return $theme;
    }

    /**
     * Get storage type
     *
     * @return string
     */
    protected function _getStorageType()
    {
        return (string)$this->_getRequest()->getParam(self::PARAM_NAME_CONTENT_TYPE);
    }

    /**
     * Get current path
     *
     * @return string
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->getStorageRoot();
            $path = $this->_getRequest()->getParam($this->_getTreeNodeName());
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
     * @throws Magento_Exception
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
                throw new Magento_Exception($this->__('Invalid type'));
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
        return $this->_session;
    }

    /**
     * Return name of parameter node
     *
     * @return string
     */
    protected function _getTreeNodeName()
    {
        return self::PARAM_NAME_NODE;
    }
}
