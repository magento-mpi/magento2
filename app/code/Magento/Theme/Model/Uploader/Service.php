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
class Magento_Theme_Model_Uploader_Service
{
    /**
     * Uploaded file path
     *
     * @var string|null
     */
    protected $_filePath;

    /**
     * File system helper
     *
     * @var Magento_Io_File
     */
    protected $_fileIo;

    /**
     * File size
     *
     * @var Magento_File_Size
     */
    protected $_fileSize;

    /**
     * File uploader
     *
     * @var Magento_Core_Model_File_Uploader
     */
    protected $_uploader;

    /**
     * @var Magento_Core_Model_File_Uploader
     */
    protected $_uploaderFactory;

    /**
     * @var  string|null
     */
    protected $_cssUploadLimit;

    /**
     * @var  string|null
     */
    protected $_jsUploadLimit;

    /**
     * Constructor
     *
     * @param Magento_Io_File $fileIo
     * @param Magento_File_Size $fileSize
     * @param Magento_Core_Model_File_UploaderFactory $uploaderFactory
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $uploadLimit
     */
    public function __construct(
        Magento_Io_File $fileIo,
        Magento_File_Size $fileSize,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        array $uploadLimits = array()
    ) {
        $this->_fileIo = $fileIo;
        $this->_fileSize = $fileSize;
        $this->_uploaderFactory = $uploaderFactory;
        if (isset($uploadLimits['css'])) {
            $this->_cssUploadLimit = $uploadLimits['css'];
        }
        if (isset($uploadLimits['js'])) {
            $this->_jsUploadLimit = $uploadLimits['js'];
        }
    }

    /**
     * Upload css file
     *
     * @param string $file - Key in the $_FILES array
     * @return array
     * @throws Magento_Core_Exception
     */
    public function uploadCssFile($file)
    {
        /** @var $fileUploader Magento_Core_Model_File_Uploader */
        $fileUploader = $this->_uploaderFactory->create(array('fileId' => $file));
        $fileUploader->setAllowedExtensions(array('css'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $isValidFileSize = $this->_validateFileSize($fileUploader->getFileSize(), $this->getCssUploadMaxSize());
        if (!$isValidFileSize) {
            throw new Magento_Core_Exception(__(
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
     * @throws Magento_Core_Exception
     */
    public function uploadJsFile($file)
    {
        /** @var $fileUploader Magento_Core_Model_File_Uploader */
        $fileUploader = $this->_uploaderFactory->create(array('fileId' => $file));
        $fileUploader->setAllowedExtensions(array('js'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $isValidFileSize = $this->_validateFileSize($fileUploader->getFileSize(), $this->getJsUploadMaxSize());
        if (!$isValidFileSize) {
            throw new Magento_Core_Exception(__(
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
        $maxIniUploadSize = $this->_fileSize->getMaxFileSize();
        if (is_null($this->_cssUploadLimit)) {
            return $maxIniUploadSize;
        }
        $maxCssUploadSize = $this->_fileSize->convertSizeToInteger($this->_cssUploadLimit);
        return min($maxCssUploadSize, $maxIniUploadSize);
    }

    /**
     * Get js upload max size
     *
     * @return int
     */
    public function getJsUploadMaxSize()
    {
        $maxIniUploadSize = $this->_fileSize->getMaxFileSize();
        if (is_null($this->_jsUploadLimit)) {
            return $maxIniUploadSize;
        }
        $maxJsUploadSize = $this->_fileSize->convertSizeToInteger($this->_jsUploadLimit);
        return min($maxJsUploadSize, $maxIniUploadSize);
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