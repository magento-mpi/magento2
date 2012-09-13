<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * File uploader. Could be used for files upload through API
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Request_Uploader_File
{
    /**
     * Permissions for uploaded file
     */

    const CREATED_FILE_PERMISSIONS = 0666;

    /**
     * List of allowed MIME types
     *
     * @var array
     */
    protected $_mimeTypes = array();

    /**
     * Path to uploaded file
     *
     * @var string
     */
    protected $_uploadedFilePath;

    /**
     * Uploaded file name
     *
     * @var string
     */
    protected $_uploadedFileName = '';

    /**
     * File name in case if it was not specified in file data
     *
     * @var string
     */
    protected $_defaultFileName = 'file';

    /**
     * Upload directory
     *
     * @var string
     */
    protected $_uploadDirectory;

    /**
     * Flag: is upload directory customized using setUploadDirectory().
     * If it false, upload directory will be deleted, or will not in other case.
     *
     * @var bool
     */
    protected $_isCustomUploadDirectory = false;

    /**
     * @var Varien_Io_File
     */
    protected $_filesystemAdapter;

    /**
     * Initialize filesystem adapter
     */
    public function __construct()
    {
        $this->_filesystemAdapter = new Varien_Io_File();
        $this->_uploadDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . uniqid();
    }

    /**
     * Create temporary file on server using $fileData. File content is expected to be base64-encoded
     *
     * @param array $fileData
     * format: array('file_content' => $base64EncodedFile, 'file_mime_type' => $mimeType, 'file_name' => $fileName)
     * @return string Path on server to uploaded temporary file
     * @throws Mage_Webapi_Exception
     */
    public function upload($fileData)
    {
        $this->_validateFileData($fileData);
        $fileContent = base64_decode($fileData['file_content'], true);
        unset($fileData['file_content']);
        try {
            $this->_filesystemAdapter->checkAndCreateFolder($this->_uploadDirectory);
            $this->_filesystemAdapter->open(array('path' => $this->_uploadDirectory));
            $this->_uploadedFileName = $this->_getUniqueFileName($fileData);
            $this->_filesystemAdapter->write($this->_uploadedFileName, $fileContent, self::CREATED_FILE_PERMISSIONS);
            unset($fileContent);
        } catch (Mage_Core_Exception $e) {
            throw new Mage_Webapi_Exception($e->getMessage(), Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Webapi_Exception('Resource unknown error.', Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR);
        }
        $this->_uploadedFilePath = $this->_uploadDirectory . DS . $this->_uploadedFileName;
        return $this->_uploadedFilePath;
    }

    /**
     * Validate file data, return false if data is valid or error message otherwise
     *
     * @param array $fileData
     * @return string|bool
     */
    public function validate($fileData)
    {
        $errorMessage = false;
        try {
            $this->_validateFileData($fileData);
        } catch (Mage_Webapi_Exception $e) {
            $errorMessage = $e->getMessage();
        }
        return $errorMessage;
    }

    /**
     * Add specified MIME types to the list of allowed ones
     *
     * @param array $allowedMimeTypes
     */
    public function addAllowedMimeTypes($allowedMimeTypes = array())
    {
        $this->_mimeTypes = array_merge($this->_mimeTypes, $allowedMimeTypes);
    }

    /**
     * Set directory into which file is uploaded
     *
     * @param string $uploadDirectory
     */
    public function setUploadDirectory($uploadDirectory)
    {
        $this->_uploadDirectory = $uploadDirectory;
        $this->_isCustomUploadDirectory = true;
    }

    /**
     * Get directory into which file is uploaded
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return $this->_uploadDirectory;
    }

    /**
     * Get uploaded file name. Return empty string if no file was uploaded yet
     *
     * @return string
     */
    public function getUploadedFileName()
    {
        return $this->_uploadedFileName;
    }

    /**
     * Remove created file (with temp directory). Can be useful if temporary file was created
     */
    public function deleteUploadedFile()
    {
        if ($this->_uploadedFilePath) {
            if ($this->_isCustomUploadDirectory) {
                // if upload directory was set using getUploadDirectory(), delete only uploaded file
                $this->_filesystemAdapter->rm($this->_uploadedFilePath);
            } else {
                // if upload directory is default (temporary) it should be deleted completely with entire file
                $this->_filesystemAdapter->rmdir($this->_uploadDirectory, true);
            }
        }
    }

    /**
     * Perform file data validation
     *
     * @param array $fileData
     * @throws Mage_Webapi_Exception
     */
    protected function _validateFileData($fileData)
    {
        if (!is_array($fileData)) {
            throw new Mage_Webapi_Exception("File data is expected to be an array.",
                Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
        if (!isset($fileData['file_content']) || empty($fileData['file_content'])) {
            throw new Mage_Webapi_Exception("'file_content' is not specified.", Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
        if (!isset($fileData['file_mime_type']) || empty($fileData['file_mime_type'])) {
            throw new Mage_Webapi_Exception("'file_mime_type' is not specified.",
                Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
        // if MIME type is invalid exception will be thrown
        $this->_getExtensionByMimeType($fileData['file_mime_type']);
        $fileContent = @base64_decode($fileData['file_content'], true);
        if (!$fileContent) {
            throw new Mage_Webapi_Exception('File content must be base64 encoded.',
                Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Calculate file name with extension based on file data.
     * If file with specified file name already exists - generate unique name
     *
     * @param array $fileData
     * @return string
     */
    protected function _getUniqueFileName($fileData)
    {
        if (isset($fileData['file_name']) && $fileData['file_name']) {
            $fileName = $fileData['file_name'];
        } else {
            $fileName = uniqid("{$this->_defaultFileName}_", true);
        }
        $extension = $this->_getExtensionByMimeType($fileData['file_mime_type']);
        if ($this->_filesystemAdapter->fileExists($this->_uploadDirectory . $fileName . $extension)) {
            $fileName = uniqid("{$fileName}_", true);
        }
        return $fileName . $extension;
    }

    /**
     * Retrieve file extension using its MIME type
     *
     * @throws Mage_Webapi_Exception
     * @param string $mimeType
     * @return string
     */
    protected function _getExtensionByMimeType($mimeType)
    {
        if (!is_string($mimeType) || !array_key_exists($mimeType, $this->_mimeTypes)) {
            throw new Mage_Webapi_Exception('Unsuppoted file MIME type', Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
        return '.' . $this->_mimeTypes[$mimeType];
    }

}
