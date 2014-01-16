<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\File\Storage\Directory;

/**
 * Class Database
 */
class Database extends \Magento\Core\Model\File\Storage\Database\AbstractDatabase
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
     * @var \Magento\Core\Model\File\Storage\Directory\DatabaseFactory
     */
    protected $_directoryFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\File\Storage\Directory\DatabaseFactory $directoryFactory
     * @param \Magento\Core\Model\Resource\File\Storage\Directory\Database $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param string|null $connectionName
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Core\Model\Date $dateModel,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\File\Storage\Directory\DatabaseFactory $directoryFactory,
        \Magento\Core\Model\Resource\File\Storage\Directory\Database $resource,
        \Magento\Data\Collection\Db $resourceCollection = null,
        $connectionName = null,
        array $data = array()
    ) {
        $this->_directoryFactory = $directoryFactory;
        parent::__construct(
            $context,
            $registry,
            $coreFileStorageDb,
            $dateModel,
            $app,
            $resource,
            $resourceCollection,
            $connectionName,
            $data
        );
        $this->_init(get_class($this->_resource));
    }

    /**
     * Load object data by path
     *
     * @param  string $path
     * @return \Magento\Core\Model\File\Storage\Directory\Database
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
     * @return \Magento\Core\Model\File\Storage\Directory\Database
     */
    public function createRecursive($path)
    {
        $directory = $this->_directoryFactory->create()->loadByPath($path);

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
        $offset = ((int)$offset >= 0) ? (int)$offset : 0;
        $count  = ((int)$count >= 1) ? (int)$count : 1;

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
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\File\Storage\Directory\Database
     */
    public function importDirectories($dirs)
    {
        if (!is_array($dirs)) {
            return $this;
        }

        $dateSingleton = $this->_date;
        foreach ($dirs as $dir) {
            if (!is_array($dir) || !isset($dir['name']) || !strlen($dir['name'])) {
                continue;
            }

            try {
                $dir['path'] = ltrim($dir['path'], '.');
                $directory = $this->_directoryFactory->create(array('connectionName' => $this->getConnectionName()));
                $directory->setPath($dir['path']);

                $parentId = $directory->getParentId();
                if ($parentId || $dir['path'] == '') {
                    $directory->setName($dir['name']);
                    $directory->setUploadTime($dateSingleton->date());
                    $directory->save();
                } else {
                    throw new \Magento\Core\Exception(__('Parent directory does not exist: %1', $dir['path']));
                }
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        return $this;
    }

    /**
     * Clean directories at storage
     *
     * @return \Magento\Core\Model\File\Storage\Directory\Database
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
        $directory = $this->_coreFileStorageDb->getMediaRelativePath($directory);

        return $this->_getResource()->getSubdirectories($directory);
    }

    /**
     * Delete directory from database
     *
     * @param string $dirPath
     * @return \Magento\Core\Model\File\Storage\Directory\Database
     */
    public function deleteDirectory($dirPath)
    {
        $dirPath = $this->_coreFileStorageDb->getMediaRelativePath($dirPath);
        $name = basename($dirPath);
        $path = dirname($dirPath);

        if ('.' == $path) {
            $path = '';
        }

        $this->_getResource()->deleteDirectory($name, $path);

        return $this;
    }
}
