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
 * Abstract model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_File_Storage_File extends Magento_Core_Model_File_Storage_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_file';

    /**
     * Data at storage
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Collect errors during sync process
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Class construct
     */
    public function __construct(
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDb,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_File_Storage_File $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($coreFileStorageDb, $context, $registry, $resource, $resourceCollection, $data);
        $this->_setResourceModel('Magento_Core_Model_Resource_File_Storage_File');
    }

    /**
     * Initialization
     *
     * @return Magento_Core_Model_File_Storage_File
     */
    public function init()
    {
        return $this;
    }

    /**
     * Return storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return __('File system');
    }

    /**
     * Get files and directories from storage
     *
     * @return array
     */
    public function getStorageData()
    {
        return $this->_getResource()->getStorageData();
    }

    /**
     * Check if there was errors during sync process
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * Clear files and directories in storage
     *
     * @return Magento_Core_Model_File_Storage_File
     */
    public function clear()
    {
        $this->_getResource()->clear();
        return $this;
    }

    /**
     * Collect files and directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @param  string $type
     * @return array|bool
     */
    public function collectData($offset = 0, $count = 100, $type = 'files')
    {
        if (!in_array($type, array('files', 'directories'))) {
            return false;
        }

        $offset = ((int) $offset >= 0) ? (int) $offset : 0;
        $count  = ((int) $count >= 1) ? (int) $count : 1;

        if (is_null($this->_data)) {
            $this->_data = $this->getStorageData();
        }

        $slice = array_slice($this->_data[$type], $offset, $count);
        if (empty($slice)) {
            return false;
        }

        return $slice;
    }

    /**
     * Export directories list from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        return $this->collectData($offset, $count, 'directories');
    }

    /**
     * Export files list in defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 1)
    {
        $slice = $this->collectData($offset, $count, 'files');

        if (!$slice) {
            return false;
        }

        $result = array();
        foreach ($slice as $fileName) {
            try {
                $fileInfo = $this->collectFileInfo($fileName);
            } catch (Exception $e) {
                Mage::logException($e);
                continue;
            }

            $result[] = $fileInfo;
        }

        return $result;
    }

    /**
     * Import entities to storage
     *
     * @param  array $data
     * @param  string $callback
     * @return Magento_Core_Model_File_Storage_File
     */
    public function import($data, $callback)
    {
        if (!is_array($data) || !method_exists($this, $callback)) {
            return $this;
        }

        foreach ($data as $part) {
            try {
                $this->$callback($part);
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     * @return Magento_Core_Model_File_Storage_File
     */
    public function importDirectories($dirs)
    {
        return $this->import($dirs, 'saveDir');
    }

    /**
     * Import files list
     *
     * @param  array $files
     * @return Magento_Core_Model_File_Storage_File
     */
    public function importFiles($files)
    {
        return $this->import($files, 'saveFile');
    }

    /**
     * Save directory to storage
     *
     * @param  array $dir
     * @return bool
     */
    public function saveDir($dir)
    {
        return $this->_getResource()->saveDir($dir);
    }

    /**
     * Save file to storage
     *
     * @param  array|Magento_Core_Model_File_Storage_Database $file
     * @param  bool $overwrite
     * @return bool|int
     */
    public function saveFile($file, $overwrite = true)
    {
        if (isset($file['filename']) && !empty($file['filename'])
            && isset($file['content']) && !empty($file['content'])
        ) {
            try {
                $filename = (isset($file['directory']) && !empty($file['directory']))
                    ? $file['directory'] . DS . $file['filename']
                    : $file['filename'];

                return $this->_getResource()
                    ->saveFile($filename, $file['content'], $overwrite);
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::throwException(__('Unable to save file "%1" at "%2"', $file['filename'], $file['directory']));
            }
        } else {
            Mage::throwException(__('Wrong file info format'));
        }

        return false;
    }
}
