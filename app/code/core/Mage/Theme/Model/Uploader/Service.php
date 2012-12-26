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
 * Theme file uploader service
 */
class Mage_Theme_Model_Uploader_Service extends Mage_Core_Model_Abstract
{
    /**
     * Css file upload limit
     */
    const XML_PATH_CSS_UPLOAD_LIMIT = 'global/theme/css/upload_limit';

    /**
     * Uploaded file path
     *
     * @var string|null
     */
    protected $_filePath;

    /**
     * File system helper
     *
     * @var Varien_Io_File
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
     * @var Mage_Core_Model_File_Uploader
     */
    protected $_uploader;

    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Varien_Io_File $fileIo
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param Magento_File_Size $fileSize
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Varien_Io_File $fileIo,
        Magento_File_Size $fileSize,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_fileIo = $fileIo;
        $this->_fileSize = $fileSize;
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
    }

    /**
     * Upload css file
     *
     * @param string $type
     * @return Mage_Theme_Model_Uploader_Service
     */
    public function uploadCssFile($type)
    {
        /** @var $fileUploader Mage_Core_Model_File_Uploader */
        $fileUploader = Mage::getObjectManager()->get('Mage_Core_Model_File_Uploader', array($type));
        $fileUploader->setAllowedExtensions(array('css'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $this->_validateCssMaxFileSize($fileUploader->getFileSize());

        $file = $fileUploader->validateFile();
        $this->setFilePath($file['tmp_name']);
        return $this;
    }

    /**
     * Get uploaded file content
     *
     * @return string
     */
    public function getFileContent()
    {
        return $this->_fileIo->read($this->getFilePath());
    }

    /**
     * Get css upload max size
     *
     * @return int
     */
    public function getCssUploadMaxSize()
    {
        $maxCssUploadSize = $this->_fileSize->convertSizeToInteger(
            (string)Mage::getConfig()->getNode(self::XML_PATH_CSS_UPLOAD_LIMIT)
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
     * Validate CSS max file size
     *
     * @param int $fileSize
     * @return Mage_Theme_Model_Uploader_Service
     */
    protected function _validateCssMaxFileSize($fileSize)
    {
        if ($fileSize > $this->getCssUploadMaxSize()) {
            Mage::throwException("File size should be less than {$this->getCssUploadMaxSizeInMb()}M.");
        }
        return $this;
    }
}
