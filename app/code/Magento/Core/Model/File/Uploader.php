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
namespace Magento\Core\Model\File;

class Uploader extends \Magento\File\Uploader
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
    protected $_coreFileStorageDb = null;

    /**
     * Init upload
     *
     * @param Magento_Core_Helper_File_Storage_Database $coreFileStorageDb
     * @param Magento_Core_Helper_File_Storage $coreFileStorage
     * @param $fileId
     */
    public function __construct(
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDb,
        Magento_Core_Helper_File_Storage $coreFileStorage,
        $fileId
    ) {
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_coreFileStorage = $coreFileStorage;
        parent::__construct($fileId);
    }

    /**
     * Save file to storage
     *
     * @param  array $result
     * @return \Magento\Core\Model\File\Uploader
     */
    protected function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }

        if ($this->_coreFileStorage->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        $this->_result['file'] = $this->_coreFileStorageDb->saveUploadedFile($result);

        return $this;
    }

    /**
     * Getter/Setter for _skipDbProcessing flag
     *
     * @param null|bool $flag
     * @return bool|\Magento\Core\Model\File\Uploader
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
        /** @var $validator \Magento\Core\Model\File\Validator\NotProtectedExtension */
        $validator = \Mage::getSingleton('Magento\Core\Model\File\Validator\NotProtectedExtension');
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
