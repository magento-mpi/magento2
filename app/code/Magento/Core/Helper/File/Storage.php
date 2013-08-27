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
    protected $_coreFileStorageDatabase = null;

    /**
     * @param Magento_Core_Helper_File_Storage_Database $coreFileStorageDatabase
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDatabase,
        Magento_Core_Helper_Context $context
    ) {
        $this->_coreFileStorageDatabase = $coreFileStorageDatabase;
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
            $this->_currentStorage = (int) Mage::app()
                ->getConfig()->getNode(Magento_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA);
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
        return Mage::getSingleton('Magento_Core_Model_File_Storage')->getStorageModel($storage, $params);
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

        $dbHelper = $this->_coreFileStorageDatabase;

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
