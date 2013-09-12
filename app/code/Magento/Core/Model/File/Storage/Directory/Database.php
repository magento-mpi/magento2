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
 * Directory database storage model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_File_Storage_Directory_Database extends Magento_Core_Model_File_Storage_Database_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_directory_database';

    /**
     * Collect errors during sync process
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Class construct
     *
     * @param string $connectionName
     */
    public function __construct($connectionName = null)
    {
        $this->_init('Magento_Core_Model_Resource_File_Storage_Directory_Database');

        parent::__construct($connectionName);
    }

    /**
     * Load object data by path
     *
     * @param  string $path
     * @return Magento_Core_Model_File_Storage_Directory_Database
     */
    public function loadByPath($path)
    {
        /**
         * Clear model data
         * addData() is used because it's needed to clear only db storaged data
         */
        $this->addData(
            array(
                'directory_id'  => null,
                'name'          => null,
                'path'          => null,
                'upload_time'   => null,
                'parent_id'     => null
            )
        );

        $this->_getResource()->loadByPath($this, $path);
        return $this;
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
     * Retrieve directory parent id
     *
     * @return int
     */
    public function getParentId()
    {
        if (!$this->getData('parent_id')) {
            $parentId = $this->_getResource()->getParentId($this->getPath());
            if (empty($parentId)) {
                $parentId = null;
            }

            $this->setData('parent_id', $parentId);
        }

        return $parentId;
    }

    /**
     * Create directories recursively
     *
     * @param  string $path
     * @return Magento_Core_Model_File_Storage_Directory_Database
     */
    public function createRecursive($path)
    {
        $directory = Mage::getModel('Magento_Core_Model_File_Storage_Directory_Database')->loadByPath($path);

        if (!$directory->getId()) {
            $dirName = basename($path);
            $dirPath = dirname($path);

            if ($dirPath != '.') {
                $parentDir = $this->createRecursive($dirPath);
                $parentId = $parentDir->getId();
            } else {
                $dirPath = '';
                $parentId = null;
            }

            $directory->setName($dirName);
            $directory->setPath($dirPath);
            $directory->setParentId($parentId);
            $directory->save();
        }

        return $directory;
    }

    /**
     * Export directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return bool
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        $offset = ((int) $offset >= 0) ? (int) $offset : 0;
        $count  = ((int) $count >= 1) ? (int) $count : 1;

        $result = $this->_getResource()->exportDirectories($offset, $count);

        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     * @return Magento_Core_Model_File_Storage_Directory_Database
     */
    public function importDirectories($dirs)
    {
        if (!is_array($dirs)) {
            return $this;
        }

        $dateSingleton = Mage::getSingleton('Magento_Core_Model_Date');
        foreach ($dirs as $dir) {
            if (!is_array($dir) || !isset($dir['name']) || !strlen($dir['name'])) {
                continue;
            }

            try {
                $arguments = array('connection' => $this->getConnectionName());
                $directory = Mage::getModel(
                    'Magento_Core_Model_File_Storage_Directory_Database',
                    array('connectionName' => $arguments)
                );
                $directory->setPath($dir['path']);

                $parentId = $directory->getParentId();
                if ($parentId || $dir['path'] == '') {
                    $directory->setName($dir['name']);
                    $directory->setUploadTime($dateSingleton->date());
                    $directory->save();
                } else {
                    Mage::throwException(__('Parent directory does not exist: %1', $dir['path']));
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Clean directories at storage
     *
     * @return Magento_Core_Model_File_Storage_Directory_Database
     */
    public function clearDirectories()
    {
        $this->_getResource()->clearDirectories();
        return $this;
    }

    /**
     * Return subdirectories
     *
     * @param string $directory
     * @return mixed
     */
    public function getSubdirectories($directory)
    {
        $directory = Mage::helper('Magento_Core_Helper_File_Storage_Database')->getMediaRelativePath($directory);

        return $this->_getResource()->getSubdirectories($directory);
    }

    /**
     * Delete directory from database
     *
     * @param string $path
     * @return Magento_Core_Model_File_Storage_Directory_Database
     */
    public function deleteDirectory($dirPath)
    {
        $dirPath = Mage::helper('Magento_Core_Helper_File_Storage_Database')->getMediaRelativePath($dirPath);
        $name = basename($dirPath);
        $path = dirname($dirPath);

        if ('.' == $path) {
            $path = '';
        }

        $this->_getResource()->deleteDirectory($name, $path);

        return $this;
    }
}
