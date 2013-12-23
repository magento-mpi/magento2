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
namespace Magento\Cms\Helper\Wysiwyg;

class Images extends \Magento\App\Helper\AbstractHelper
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
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $_directory;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_backendData = $backendData;
        $this->_coreData = $coreData;
        $this->_storeManager = $storeManager;

        $this->_directory = $filesystem->getDirectoryWrite(\Magento\Filesystem::MEDIA);
        $this->_directory->create(\Magento\Cms\Model\Wysiwyg\Config::IMAGE_DIRECTORY);
    }


    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return \Magento\Cms\Helper\Wysiwyg\Images
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
        return $this->_directory->getAbsolutePath(\Magento\Cms\Model\Wysiwyg\Config::IMAGE_DIRECTORY);
    }

    /**
     * Images Storage base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_MEDIA);
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
        if ($id === \Magento\Theme\Helper\Storage::NODE_ROOT) {
            return $this->getStorageRoot();
        } else {
            return $this->getStorageRoot() . $this->idDecode($id);
        }
    }
    
    /*
     * Check whether using static URLs is allowed
     *
     * @return boolean
     */
    public function isUsingStaticUrlsAllowed()
    {
        $checkResult = new \StdClass;
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
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_MEDIA);
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
                    'cms/wysiwyg/directive',
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
     * @throws \Magento\Core\Exception
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->_directory->getAbsolutePath() . \Magento\Cms\Model\Wysiwyg\Config::IMAGE_DIRECTORY;
            $path = $this->_getRequest()->getParam($this->getTreeNodeName());
            if ($path) {
                $path = $this->convertIdToPath($path);
                if ($this->_directory->isDirectory($this->_directory->getRelativePath($path))) {
                    $currentPath = $path;
                }
            }
            try {
                $currentDir = $this->_directory->getRelativePath($currentPath);
                if (!$this->_directory->isExist($currentDir)) {
                    $this->_directory->create($currentDir);
                }
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $message = __('The directory %1 is not writable by server.', $currentPath);
                throw new \Magento\Core\Exception($message);
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
            $path = $this->getCurrentPath();
            $mediaUrl = $this->_storeManager->getStore($this->_storeId)
                ->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_MEDIA);
            $this->_currentUrl = $mediaUrl . $this->_directory->getRelativePath($path) . '/';
        }
        return $this->_currentUrl;
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

    /**
     * Is path under storage root directory
     *
     * @param string $path
     *
     * @throws \Magento\Core\Exception
     */
    public function validatePath($path)
    {
        $root = $this->sanitizePath($this->getStorageRoot());
        if ($root == $path) {
            throw new \Magento\Core\Exception(__('We cannot delete root directory %1.', $path));
        }
        if (strpos($path, $root) !== 0) {
            throw new \Magento\Core\Exception(__('Directory %1 is not under storage root path.', $path));
        }
    }

    /**
     * Sanitize path
     *
     * @param string $path
     *
     * @return string
     */
    public function sanitizePath($path)
    {
        return rtrim(preg_replace('~[/\\\]+~', '/', realpath($path)), '/');
    }

    /**
     * Get path in root storage dir
     *
     * @param string $path
     *
     * @return string
     */
    public function getRelativePathToRoot($path)
    {
        return substr(
            $this->sanitizePath($path),
            strlen($this->sanitizePath($this->getStorageRoot()))
        );
    }
}
