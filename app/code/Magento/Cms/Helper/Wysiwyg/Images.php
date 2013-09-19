<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg Images Helper
 */
class Magento_Cms_Helper_Wysiwyg_Images extends Magento_Core_Helper_Abstract
{

    /**
     * Current directory path
     * @var string
     */
    protected $_currentPath;

    /**
     * Current directory URL
     * @var string
     */
    protected $_currentUrl;

    /**
     * Currenty selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Dir
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Dir $dir
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Helper_Data $coreData,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Dir $dir
    ) {
        parent::__construct($context);
        $this->_eventManager = $eventManager;
        $this->_backendData = $backendData;
        $this->_coreData = $coreData;
        $this->_filesystem = $filesystem;
        $this->_dir = $dir;
        $this->_storeManager = $storeManager;

        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->getStorageRoot());
        $this->_filesystem->setWorkingDirectory($this->getStorageRoot());
    }


    /**
     * Set a specified store ID value
     *
     * @param <type> $store
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Images Storage root directory
     *
     * @return string
     */
    public function getStorageRoot()
    {
        return $this->_dir->getDir(Magento_Core_Model_Dir::MEDIA) . DS
            . Magento_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY . DS;
    }

    /**
     * Images Storage base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_MEDIA) . '/';
    }

    /**
     * Ext Tree node key name
     *
     * @return string
     */
    public function getTreeNodeName()
    {
        return 'node';
    }

    /**
     * Encode path to HTML element id
     *
     * @param string $path Path to file/directory
     * @return string
     */
    public function convertPathToId($path)
    {
        $path = str_replace($this->getStorageRoot(), '', $path);
        return $this->idEncode($path);
    }

    /**
     * Decode HTML element id
     *
     * @param string $id
     * @return string
     */
    public function convertIdToPath($id)
    {
        $path = $this->idDecode($id);
        if (!strstr($path, $this->getStorageRoot())) {
            $path = $this->getStorageRoot() . $path;
        }
        return $path;
    }

    /**
     * File system path correction
     *
     * @param string $path Original path
     * @param boolean $trim Trim slashes or not
     * @return string
     */
    public function correctPath($path, $trim = true)
    {
        $path = strtr($path, "\\\/", DS . DS);
        if ($trim) {
            $path = trim($path, DS);
        }
        return $path;
    }

    /**
     * Return file system path as Url string
     *
     * @param string $path
     * @return string
     */
    public function convertPathToUrl($path)
    {
        return str_replace(DS, '/', $path);
    }

    /**
     * Check whether using static URLs is allowed
     *
     * @return boolean
     */
    public function isUsingStaticUrlsAllowed()
    {
        $checkResult = new StdClass;
        $checkResult->isAllowed = false;
        $this->_eventManager->dispatch('cms_wysiwyg_images_static_urls_allowed', array(
            'result'   => $checkResult,
            'store_id' => $this->_storeId
        ));
        return $checkResult->isAllowed;
    }

    /**
     * Prepare Image insertion declaration for Wysiwyg or textarea(as_is mode)
     *
     * @param string $filename Filename transferred via Ajax
     * @param bool $renderAsTag Leave image HTML as is or transform it to controller directive
     * @return string
     */
    public function getImageHtmlDeclaration($filename, $renderAsTag = false)
    {
        $fileurl = $this->getCurrentUrl() . $filename;
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_MEDIA);
        $mediaPath = str_replace($mediaUrl, '', $fileurl);
        $directive = sprintf('{{media url="%s"}}', $mediaPath);
        if ($renderAsTag) {
            $html = sprintf('<img src="%s" alt="" />', $this->isUsingStaticUrlsAllowed() ? $fileurl : $directive);
        } else {
            if ($this->isUsingStaticUrlsAllowed()) {
                $html = $fileurl; // $mediaPath;
            } else {
                $directive = $this->_coreData->urlEncode($directive);
                $html = $this->_backendData->getUrl(
                    '*/cms_wysiwyg/directive',
                    array('___directive' => $directive)
                );
            }
        }
        return $html;
    }

    /**
     * Return path of the current selected directory or root directory for startup
     * Try to create target directory if it doesn't exist
     *
     * @return string
     * @throws Magento_Core_Exception
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->getStorageRoot();
            $path = $this->_getRequest()->getParam($this->getTreeNodeName());
            if ($path) {
                $path = $this->convertIdToPath($path);
                if ($this->_filesystem->isDirectory($path)) {
                    $currentPath = $path;
                }
            }
            try {
                if (!$this->_filesystem->isWritable($currentPath)) {
                    $this->_filesystem->createDirectory($currentPath);
                }
            } catch (Magento_Filesystem_Exception $e) {
                $message = __('The directory %1 is not writable by server.', $currentPath);
                throw new Magento_Core_Exception($message);
            }
            $this->_currentPath = $currentPath;
        }
        return $this->_currentPath;
    }

    /**
     * Return URL based on current selected directory or root directory for startup
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        if (!$this->_currentUrl) {
            $path = str_replace($this->_dir->getDir(Magento_Core_Model_Dir::MEDIA), '', $this->getCurrentPath());
            $path = trim($path, DS);
            $mediaUrl = $this->_storeManager->getStore($this->_storeId)
                ->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_MEDIA);
            $this->_currentUrl = $mediaUrl . $this->convertPathToUrl($path) . '/';
        }
        return $this->_currentUrl;
    }

    /**
     * Storage model singleton
     *
     * @return Magento_Cms_Model_Page_Wysiwyg_Images_Storage
     */
    public function getStorage()
    {
        return Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Images_Storage');
    }

    /**
     * Encode string to valid HTML id element, based on base64 encoding
     *
     * @param string $string
     * @return string
     */
    public function idEncode($string)
    {
        return strtr(base64_encode($string), '+/=', ':_-');
    }

    /**
     * Revert opration to idEncode
     *
     * @param string $string
     * @return string
     */
    public function idDecode($string)
    {
        $string = strtr($string, ':_-', '+/=');
        return base64_decode($string);
    }

    /**
     * Reduce filename by replacing some characters with dots
     *
     * @param string $filename
     * @param int $maxLength Maximum filename
     * @return string Truncated filename
     */
    public function getShortFilename($filename, $maxLength = 20)
    {
        if (strlen($filename) <= $maxLength) {
            return $filename;
        }
        return substr($filename, 0, $maxLength) . '...';
    }
}
