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
     * Files js model
     *
     * @var Mage_Core_Model_Theme_Files_Js
     */
    protected $_filesJs;

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
     * @param Mage_Core_Model_Theme_Files_Js $filesJs
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Varien_Io_File $fileIo,
        Magento_File_Size $fileSize,
        Mage_Core_Model_Theme_Files_Js $filesJs,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_fileIo = $fileIo;
        $this->_fileSize = $fileSize;
        $this->_filesJs = $filesJs;
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

        $isValidFileSize = $this->_validateFileSize($fileUploader->getFileSize(), $this->getCssUploadMaxSize());
        if (!$isValidFileSize) {
            Mage::throwException("CSS file size should be less than {$this->getCssUploadMaxSizeInMb()}M.");
        }

        $file = $fileUploader->validateFile();
        $this->setFilePath($file['tmp_name']);
        return $this;
    }

    /**
     * Upload js file
     *
     * @param string  $type
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Theme_Model_Uploader_Service
     */
    public function uploadJsFile($type, $theme)
    {
        /** @var $fileUploader Mage_Core_Model_File_Uploader */
        $fileUploader = Mage::getObjectManager()->get('Mage_Core_Model_File_Uploader', array($type));
        $fileUploader->setAllowedExtensions(array('js'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $isValidFileSize = $this->_validateFileSize($fileUploader->getFileSize(), $this->getJsUploadMaxSize());
        if (!$isValidFileSize) {
            Mage::throwException("JS file size should be less than {$this->getJsUploadMaxSizeInMb()}M.");
        }

        $file = $fileUploader->validateFile();
        $this->setFilePath($file['tmp_name']);
        $file['content'] = $this->getFileContent();

        $this->_filesJs->saveJsFile($theme, $file, true);

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
            (string)Mage::getConfig()->getNode($node)
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
