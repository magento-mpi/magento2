<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core file uploader model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_File_Uploader extends Magento_File_Uploader
{
    /**
     * Flag, that defines should DB processing be skipped
     *
     * @var bool
     */
    protected $_skipDbProcessing = false;

    /**
     * Core file storage
     *
     * @var Magento_Core_Helper_File_Storage
     */
    protected $_coreFileStorage = null;

    /**
     * Core file storage database
     *
     * @var Magento_Core_Helper_File_Storage_Database
     */
    protected $_coreFileStorageDatabase = null;

    /**
     * Init upload
     *
     * @param Magento_Core_Helper_File_Storage_Database $coreFileStorageDatabase
     * @param Magento_Core_Helper_File_Storage $coreFileStorage
     * @param $fileId
     */
    public function __construct(
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDatabase,
        Magento_Core_Helper_File_Storage $coreFileStorage,
        $fileId
    ) {
        $this->_coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->_coreFileStorage = $coreFileStorage;
        parent::__construct($fileId);
    }

    /**
     * Save file to storage
     *
     * @param  array $result
     * @return Magento_Core_Model_File_Uploader
     */
    protected function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }

        /** @var $helper Magento_Core_Helper_File_Storage */
        $helper = $this->_coreFileStorage;

        if ($helper->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        /** @var $dbHelper Magento_Core_Helper_File_Storage_Database */
        $dbHelper = $this->_coreFileStorageDatabase;
        $this->_result['file'] = $dbHelper->saveUploadedFile($result);

        return $this;
    }

    /**
     * Getter/Setter for _skipDbProcessing flag
     *
     * @param null|bool $flag
     * @return bool|Magento_Core_Model_File_Uploader
     */
    public function skipDbProcessing($flag = null)
    {
        if (is_null($flag)) {
            return $this->_skipDbProcessing;
        }
        $this->_skipDbProcessing = (bool)$flag;
        return $this;
    }

    /**
     * Check protected/allowed extension
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        //validate with protected file types
        /** @var $validator Magento_Core_Model_File_Validator_NotProtectedExtension */
        $validator = Mage::getSingleton('Magento_Core_Model_File_Validator_NotProtectedExtension');
        if (!$validator->isValid($extension)) {
            return false;
        }

        return parent::checkAllowedExtension($extension);
    }

    /**
     * Get file size
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->_file['size'];
    }

    /**
     * Validate file
     *
     * @return array
     */
    public function validateFile()
    {
        $this->_validateFile();
        return $this->_file;
    }
}
