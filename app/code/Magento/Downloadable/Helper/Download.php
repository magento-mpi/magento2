<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Helper;

/**
 * Downloadable Products Download Helper
 */
class Download extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Link type url
     */
    const LINK_TYPE_URL = 'url';

    /**
     * Link type file
     */
    const LINK_TYPE_FILE = 'file';

    /**
     * Config path to content disposition
     */
    const XML_PATH_CONTENT_DISPOSITION = 'catalog/downloadable/content_disposition';

    /**
     * Type of link
     *
     * @var string
     */
    protected $_linkType = self::LINK_TYPE_FILE;

    /**
     * Resource file
     *
     * @var string
     */
    protected $_resourceFile = null;

    /**
     * Resource open handle
     *
     * @var resource
     */
    protected $_handle = null;

    /**
     * Remote server headers
     *
     * @var array
     */
    protected $_urlHeaders = array();

    /**
     * MIME Content-type for a file
     *
     * @var string
     */
    protected $_contentType = 'application/octet-stream';

    /**
     * File name
     *
     * @var string
     */
    protected $_fileName = 'download';

    /**
     * Core file storage database
     *
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDb;

    /**
     * Downloadable file
     *
     * @var \Magento\Downloadable\Helper\File
     */
    protected $_downloadableFile;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * Media Directory (readable).
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $_mediaDirectory;

    /**
     * Directory to access files via socket connections (using http or https schemes)
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $_socketDirectory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Downloadable\Helper\File $downloadableFile
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\App $app,
        \Magento\Filesystem $filesystem
    )
    {
        $this->_coreData = $coreData;
        $this->_downloadableFile = $downloadableFile;
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_app = $app;
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryRead($filesystem::MEDIA);
        $this->_socketDirectory = $filesystem->getDirectoryRead($filesystem::SOCKET);
        parent::__construct($context);
    }

    /**
     * Retrieve Resource file handle (socket, file pointer etc)
     *
     * @return \Magento\Filesystem\File\ReadInterface
     * @throws \Magento\Core\Exception|\Exception
     */
    protected function _getHandle()
    {
        if (!$this->_resourceFile) {
            throw new \Magento\Core\Exception(__('Please set resource file and link type.'));
        }

        if (is_null($this->_handle)) {
            if ($this->_linkType == self::LINK_TYPE_URL) {
                $this->_handle = $this->_socketDirectory->openFile($this->_resourceFile);

                while ($str = $this->_handle->readLine(1024, "\r\n")) {
                    if ($str == "\r\n") {
                        break;
                    }
                    $match = array();
                    if (preg_match('#^([^:]+): (.*)\s+$#', $str, $match)) {
                        $k = strtolower($match[1]);
                        if ($k == 'set-cookie') {
                            continue;
                        } else {
                            $this->_urlHeaders[$k] = trim($match[2]);
                        }
                    } elseif (preg_match('#^HTTP/[0-9\.]+ (\d+) (.*)\s$#', $str, $match)) {
                        $this->_urlHeaders['code'] = $match[1];
                        $this->_urlHeaders['code-string'] = trim($match[2]);
                    }
                }

                if (!isset($this->_urlHeaders['code']) || $this->_urlHeaders['code'] != 200) {
                    throw new \Magento\Core\Exception(__('Something went wrong while getting the requested content.'));
                }
            } elseif ($this->_linkType == self::LINK_TYPE_FILE) {
                $fileExists = $this->_downloadableFile->ensureFileInFilesystem($this->_resourceFile);
                if ($fileExists) {
                    $this->_handle = $this->_mediaDirectory->openFile($this->_resourceFile, '???');
                } else {
                    throw new \Magento\Core\Exception(__('Invalid download link type.'));
                }
            } else {
                throw new \Magento\Core\Exception(__('Invalid download link type.'));
            }
        }
        return $this->_handle;
    }

    /**
     * Retrieve file size in bytes
     */
    public function getFilesize()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            // @TODO
            return $handle->streamStat('size');
        } elseif ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-length'])) {
                return $this->_urlHeaders['content-length'];
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            if (function_exists('mime_content_type') && ($contentType = mime_content_type($this->_resourceFile))) {
                return $contentType;
            } else {
                return $this->_downloadableFile->getFileType($this->_resourceFile);
            }
        } elseif ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-type'])) {
                $contentType = explode('; ', $this->_urlHeaders['content-type']);
                return $contentType[0];
            }
        }
        return $this->_contentType;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            return pathinfo($this->_resourceFile, PATHINFO_BASENAME);
        } elseif ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-disposition'])) {
                $contentDisposition = explode('; ', $this->_urlHeaders['content-disposition']);
                if (!empty($contentDisposition[1]) && strpos($contentDisposition[1], 'filename=') !== false) {
                    return substr($contentDisposition[1], 9);
                }
            }
            $fileName = @pathinfo($this->_resourceFile, PATHINFO_BASENAME);
            if ($fileName) {
                return $fileName;
            }
        }
        return $this->_fileName;
    }

    /**
     * Set resource file for download
     *
     * @param string $resourceFile
     * @param string $linkType
     * @return \Magento\Downloadable\Helper\Download
     */
    public function setResource($resourceFile, $linkType = self::LINK_TYPE_FILE)
    {
        if (self::LINK_TYPE_FILE == $linkType) {
            //check LFI protection
            $this->_filesystem->checkLfiProtection($resourceFile);

            $resourceFile = $this->_mediaDirectory->getAbsolutePath($resourceFile);
        }

        $this->_resourceFile = $resourceFile;
        $this->_linkType = $linkType;

        return $this;
    }

    /**
     * Retrieve Http Request Object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getHttpRequest()
    {
        return $this->_app->getFrontController()->getRequest();
    }

    /**
     * Retrieve Http Response Object
     *
     * @return \Magento\App\ResponseInterface
     */
    public function getHttpResponse()
    {
        return $this->_app->getFrontController()->getResponse();
    }

    public function output()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            while (true == ($buffer = $handle->read(1024))) {
                print $buffer;
            }
        } elseif ($this->_linkType == self::LINK_TYPE_URL) {
            while (!$handle->eof()) {
                print $handle->readLine(1024);
            }
        }
    }

    /**
     * Use Content-Disposition: attachment
     *
     * @param mixed $store
     * @return bool
     */
    public function getContentDisposition($store = null)
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_CONTENT_DISPOSITION, $store);
    }
}
