<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme file uploader service
 */
namespace Magento\Theme\Model\Uploader;

class Service
{
    /**
     * Css file upload limit
     */
    const XML_PATH_CSS_UPLOAD_LIMIT = 'global/theme/css/upload_limit';

    /**
     * Js file upload limit
     */
    const XML_PATH_JS_UPLOAD_LIMIT = 'global/theme/js/upload_limit';

    /**
     * Uploaded file path
     *
     * @var string|null
     */
    protected $_filePath;

    /**
     * File system helper
     *
     * @var \Magento\Io\File
     */
    protected $_fileIo;

    /**
     * File size
     *
     * @var \Magento\File\Size
     */
    protected $_fileSize;

    /**
     * File uploader
     *
     * @var \Magento\Core\Model\File\Uploader
     */
    protected $_uploader;

    /**
     * @var \Magento\Core\Model\File\Uploader
     */
    protected $_uploaderFactory;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Io\File $fileIo
     * @param \Magento\File\Size $fileSize
     * @param \Magento\Core\Model\File\UploaderFactory $uploaderFactory
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Io_File $fileIo,
        Magento_File_Size $fileSize,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_fileIo = $fileIo;
        $this->_fileSize = $fileSize;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Upload css file
     *
     * @param string $file - Key in the $_FILES array
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function uploadCssFile($file)
    {
        /** @var $fileUploader \Magento\Core\Model\File\Uploader */
        $fileUploader = $this->_uploaderFactory->create(array('fileId' => $file));
        $fileUploader->setAllowedExtensions(array('css'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $isValidFileSize = $this->_validateFileSize($fileUploader->getFileSize(), $this->getCssUploadMaxSize());
        if (!$isValidFileSize) {
            throw new \Magento\Core\Exception(__(
                'The CSS file must be less than %1M.', $this->getCssUploadMaxSizeInMb()
            ));
        }

        $file = $fileUploader->validateFile();
        return array('filename' => $file['name'], 'content' => $this->getFileContent($file['tmp_name']));
    }

    /**
     * Upload js file
     *
     * @param string $file - Key in the $_FILES array
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function uploadJsFile($file)
    {
        /** @var $fileUploader \Magento\Core\Model\File\Uploader */
        $fileUploader = $this->_uploaderFactory->create(array('fileId' => $file));
        $fileUploader->setAllowedExtensions(array('js'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $isValidFileSize = $this->_validateFileSize($fileUploader->getFileSize(), $this->getJsUploadMaxSize());
        if (!$isValidFileSize) {
            throw new \Magento\Core\Exception(__(
                'The JS file must be less than %1M.', $this->getJsUploadMaxSizeInMb()
            ));
        }

        $file = $fileUploader->validateFile();
        return array('filename' => $file['name'], 'content' => $this->getFileContent($file['tmp_name']));
    }

    /**
     * Get uploaded file content
     *
     * @param string $filePath
     * @return string
     */
    public function getFileContent($filePath)
    {
        return $this->_fileIo->read($filePath);
    }

    /**
     * Get css upload max size
     *
     * @return int
     */
    public function getCssUploadMaxSize()
    {
        return $this->_getMaxUploadSize(self::XML_PATH_CSS_UPLOAD_LIMIT);
    }

    /**
     * Get js upload max size
     *
     * @return int
     */
    public function getJsUploadMaxSize()
    {
        return $this->_getMaxUploadSize(self::XML_PATH_JS_UPLOAD_LIMIT);
    }

    /**
     * Get max upload size
     *
     * @param string $node
     * @return int
     */
    protected function _getMaxUploadSize($node)
    {
        $maxCssUploadSize = $this->_fileSize->convertSizeToInteger(
            (string)$this->_coreConfig->getNode($node)
        );
        $maxIniUploadSize = $this->_fileSize->getMaxFileSize();
        return min($maxCssUploadSize, $maxIniUploadSize);
    }

    /**
     * Get css upload max size in megabytes
     *
     * @return float
     */
    public function getCssUploadMaxSizeInMb()
    {
         return $this->_fileSize->getFileSizeInMb($this->getCssUploadMaxSize());
    }

    /**
     * Get js upload max size in megabytes
     *
     * @return float
     */
    public function getJsUploadMaxSizeInMb()
    {
        return $this->_fileSize->getFileSizeInMb($this->getJsUploadMaxSize());
    }

    /**
     * Validate max file size
     *
     * @param int $fileSize
     * @param int $maxFileSize
     * @return bool
     */
    protected function _validateFileSize($fileSize, $maxFileSize)
    {
        if ($fileSize > $maxFileSize) {
            return false;
        }
        return true;
    }
}
