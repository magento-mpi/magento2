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
 * File storage database model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\File\Storage;

class Database extends \Magento\Core\Model\File\Storage\Database\AbstractDatabase
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_database';

    /**
     * \Directory singleton
     *
     * @var \Magento\Core\Model\File\Storage\Directory\Database
     */
    protected $_directoryModel = null;

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
        $this->_init('Magento\Core\Model\Resource\File\Storage\Database');

        parent::__construct($connectionName);
    }

    /**
     * Retrieve directory model
     *
     * @return \Magento\Core\Model\File\Storage\Directory\Database
     */
    public function getDirectoryModel()
    {
        if (is_null($this->_directoryModel)) {
            $arguments = array('connection' => $this->getConnectionName());
            $this->_directoryModel = \Mage::getModel(
                'Magento\Core\Model\File\Storage\Directory\Database',
                array('connectionName' => $arguments));
        }

        return $this->_directoryModel;
    }

    /**
     * Create tables for file and directory storages
     *
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function init()
    {
        $this->getDirectoryModel()->prepareStorage();
        $this->prepareStorage();

        return $this;
    }

    /**
     * Return storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return __('database "%1"', $this->getConnectionName());
    }

    /**
     * Load object data by filename
     *
     * @param  string $filePath
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function loadByFilename($filePath)
    {
        $filename = basename($filePath);
        $path = dirname($filePath);
        $this->_getResource()->loadByFilename($this, $filename, $path);
        return $this;
    }

    /**
     * Check if there was errors during sync process
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (!empty($this->_errors) || $this->getDirectoryModel()->hasErrors());
    }

    /**
     * Clear files and directories in storage
     *
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function clear()
    {
        $this->getDirectoryModel()->clearDirectories();
        $this->_getResource()->clearFiles();
        return $this;
    }

    /**
     * Export directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return bool|array
     */
    public function exportDirectories($offset = 0, $count = 100) {
        return $this->getDirectoryModel()->exportDirectories($offset, $count);
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     * @return \Magento\Core\Model\File\Storage\Directory\Database
     */
    public function importDirectories($dirs) {
        return $this->getDirectoryModel()->importDirectories($dirs);
    }

    /**
     * Export files list in defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 100)
    {
        $offset = ((int) $offset >= 0) ? (int) $offset : 0;
        $count  = ((int) $count >= 1) ? (int) $count : 1;

        $result = $this->_getResource()->getFiles($offset, $count);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Import files list
     *
     * @param  array $files
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function importFiles($files)
    {
        if (!is_array($files)) {
            return $this;
        }

        $dateSingleton = \Mage::getSingleton('Magento\Core\Model\Date');
        foreach ($files as $file) {
            if (!isset($file['filename']) || !strlen($file['filename']) || !isset($file['content'])) {
                continue;
            }

            try {
                $file['update_time'] = $dateSingleton->date();
                $arguments = array('connection' => $this->getConnectionName());
                $file['directory_id'] = (isset($file['directory']) && strlen($file['directory']))
                    ? \Mage::getModel(
                        'Magento\Core\Model\File\Storage\Directory\Database',
                        array('connectionName' => $arguments))
                            ->loadByPath($file['directory'])->getId()
                    : null;

                $this->_getResource()->saveFile($file);
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
                \Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Store file into database
     *
     * @param  string $filename
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function saveFile($filename)
    {
        $fileInfo = $this->collectFileInfo($filename);
        $filePath = $fileInfo['directory'];

        $directory = \Mage::getModel('Magento\Core\Model\File\Storage\Directory\Database')->loadByPath($filePath);

        if (!$directory->getId()) {
            $directory = $this->getDirectoryModel()->createRecursive($filePath);
        }

        $fileInfo['directory_id'] = $directory->getId();
        $this->_getResource()->saveFile($fileInfo);

        return $this;
    }

    /**
     * Check whether file exists in DB
     *
     * @param  string $filePath
     * @return bool
     */
    public function fileExists($filePath)
    {
        return $this->_getResource()->fileExists(basename($filePath), dirname($filePath));
    }

    /**
     * Copy files
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function copyFile($oldFilePath, $newFilePath)
    {
        $this->_getResource()->copyFile(
            basename($oldFilePath),
            dirname($oldFilePath),
            basename($newFilePath),
            dirname($newFilePath)
        );

        return $this;
    }

    /**
     * Rename files in database
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function renameFile($oldFilePath, $newFilePath)
    {
        $this->_getResource()->renameFile(
            basename($oldFilePath),
            dirname($oldFilePath),
            basename($newFilePath),
            dirname($newFilePath)
        );

        $newPath = dirname($newFilePath);
        $directory = \Mage::getModel('Magento\Core\Model\File\Storage\Directory\Database')->loadByPath($newPath);

        if (!$directory->getId()) {
            $directory = $this->getDirectoryModel()->createRecursive($newPath);
        }

        $this->loadByFilename($newFilePath);
        if ($this->getId()) {
            $this->setDirectoryId($directory->getId())->save();
        }

        return $this;
    }

    /**
     * Return directory listing
     *
     * @param string $directory
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
        $directory = \Mage::helper('Magento\Core\Helper\File\Storage\Database')->getMediaRelativePath($directory);
        return $this->_getResource()->getDirectoryFiles($directory);
    }

    /**
     * Delete file from database
     *
     * @param string $path
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function deleteFile($path)
    {
        $filename = basename($path);
        $directory = dirname($path);
        $this->_getResource()->deleteFile($filename, $directory);

        return $this;
    }
}
