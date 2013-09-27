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
 * File storage helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Helper_File_Storage extends Magento_Core_Helper_Abstract
{
    /**
     * Current storage code
     *
     * @var int
     */
    protected $_currentStorage = null;

    /**
     * List of internal storages
     *
     * @var array
     */
    protected $_internalStorageList = array(
        Magento_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM
    );

    /**
     * Core file storage database
     *
     * @var Magento_Core_Helper_File_Storage_Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * @var Magento_Core_Model_File_Storage
     */
    protected $_storage;

    /**
     * @param Magento_Core_Helper_File_Storage_Database $coreFileStorageDb
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_File_Storage_Proxy $storage
     */
    public function __construct(
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDb,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_File_Storage_Proxy $storage
    ) {
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_storage = $storage;
        parent::__construct($context);
    }

    /**
     * Return saved storage code
     *
     * @return int
     */
    public function getCurrentStorageCode()
    {
        if (is_null($this->_currentStorage)) {
            $this->_currentStorage = (int) $this->_app
                ->getConfig()->getValue(Magento_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA, 'default');
        }

        return $this->_currentStorage;
    }

    /**
     * Retrieve file system storage model
     *
     * @return Magento_Core_Model_File_Storage_File
     */
    public function getStorageFileModel()
    {
        return Mage::getSingleton('Magento_Core_Model_File_Storage_File');
    }

    /**
     * Check if storage is internal
     *
     * @param  int|null $storage
     * @return bool
     */
    public function isInternalStorage($storage = null)
    {
        $storage = (!is_null($storage)) ? (int) $storage : $this->getCurrentStorageCode();

        return in_array($storage, $this->_internalStorageList);
    }

    /**
     * Retrieve storage model
     *
     * @param  int|null $storage
     * @param  array $params
     * @return Magento_Core_Model_Abstract|bool
     */
    public function getStorageModel($storage = null, $params = array())
    {
        return $this->_storage->getStorageModel($storage, $params);
    }

    /**
     * Check if needed to copy file from storage to file system and
     * if file exists in the storage
     *
     * @param  string $filename
     * @return bool|int
     */
    public function processStorageFile($filename)
    {
        if ($this->isInternalStorage()) {
            return false;
        }

        $dbHelper = $this->_coreFileStorageDb;

        $relativePath = $dbHelper->getMediaRelativePath($filename);
        $file = $this->getStorageModel()->loadByFilename($relativePath);

        if (!$file->getId()) {
            return false;
        }

        return $this->saveFileToFileSystem($file);
    }

    /**
     * Save file to file system
     *
     * @param  Magento_Core_Model_File_Storage_Database $file
     * @return bool|int
     */
    public function saveFileToFileSystem($file)
    {
        return $this->getStorageFileModel()->saveFile($file, true);
    }

}
