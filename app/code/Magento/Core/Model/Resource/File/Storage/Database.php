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
 * File storage database resource resource model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_File_Storage_Database extends Magento_Core_Model_Resource_File_Storage_Abstract
{
    /**
     * Define table name and id field for resource
     */
    protected function _construct()
    {
        $this->_init('core_file_storage', 'file_id');
    }

    /**
     * Create database scheme for storing files
     *
     * @return Magento_Core_Model_Resource_File_Storage_Database
     */
    public function createDatabaseScheme()
    {
        $adapter = $this->_getWriteAdapter();
        $table = $this->getMainTable();
        if ($adapter->isTableExists($table)) {
            return $this;
        }

        $dirStorageTable = $this->getTable('core_directory_storage'); // For foreign key

        $ddlTable = $adapter->newTable($table)
            ->addColumn('file_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'File Id')
            ->addColumn('content', Magento_DB_Ddl_Table::TYPE_VARBINARY, Magento_DB_Ddl_Table::MAX_VARBINARY_SIZE, array(
                'nullable' => false
                ), 'File Content')
            ->addColumn('upload_time', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable' => false,
                'default' => Magento_DB_Ddl_Table::TIMESTAMP_INIT
                ), 'Upload Timestamp')
            ->addColumn('filename', Magento_DB_Ddl_Table::TYPE_TEXT, 100, array(
                'nullable' => false
                ), 'Filename')
            ->addColumn('directory_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'default' => null
                ), 'Identifier of Directory where File is Located')
            ->addColumn('directory', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
                'default' => null
                ), 'Directory Path')
            ->addIndex($adapter->getIndexName($table, array('filename', 'directory_id'),
                Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
                array('filename', 'directory_id'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
            ->addIndex($adapter->getIndexName($table, array('directory_id')), array('directory_id'))
            ->addForeignKey($adapter->getForeignKeyName($table, 'directory_id', $dirStorageTable, 'directory_id'),
                'directory_id', $dirStorageTable, 'directory_id',
                Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
            ->setComment('File Storage');

        $adapter->createTable($ddlTable);
        return $this;
    }

    /**
     * Decodes blob content retrieved by DB driver
     *
     * @param  array $row Table row with 'content' key in it
     * @return array
     */
    protected function _decodeFileContent($row)
    {
        $row['content'] = $this->_getReadAdapter()->decodeVarbinary($row['content']);
        return $row;
    }

    /**
     * Decodes blob content retrieved by Database driver
     *
     * @param  array $rows Array of table rows (files), each containing 'content' key
     * @return array
     */
    protected function _decodeAllFilesContent($rows)
    {
        foreach ($rows as $key => $row) {
            $rows[$key] = $this->_decodeFileContent($row);
        }
        return $rows;
    }

    /**
     * Load entity by filename
     *
     * @param  Magento_Core_Model_File_Storage_Database $object
     * @param  string $filename
     * @param  string $path
     * @return Magento_Core_Model_Resource_File_Storage_Database
     */
    public function loadByFilename(Magento_Core_Model_File_Storage_Database $object, $filename, $path)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('filename = ?', $filename)
            ->where($adapter->prepareSqlCondition('directory', array('seq' => $path)));

        $row = $adapter->fetchRow($select);
        if ($row) {
            $row = $this->_decodeFileContent($row);
            $object->setData($row);
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * Clear files in storage
     *
     * @return Magento_Core_Model_Resource_File_Storage_Database
     */
    public function clearFiles()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable());

        return $this;
    }

    /**
     * Get files from storage at defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array
     */
    public function getFiles($offset = 0, $count = 100)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array('filename', 'content', 'directory')
            )
            ->order('file_id')
            ->limit($count, $offset);

        $rows = $adapter->fetchAll($select);
        return $this->_decodeAllFilesContent($rows);
    }

    /**
     * Save file to storage
     *
     * @param  Magento_Core_Model_File_Storage_Database|array $object
     * @return Magento_Core_Model_Resource_File_Storage_Database
     */
    public function saveFile($file)
    {
        $adapter = $this->_getWriteAdapter();

        $contentParam = new Magento_DB_Statement_Parameter($file['content']);
        $contentParam->setIsBlob(true);
        $data = array(
            'content'        => $contentParam,
            'upload_time'    => $file['update_time'],
            'filename'       => $file['filename'],
            'directory_id'   => $file['directory_id'],
            'directory'      => $file['directory']
        );

        $adapter->insertOnDuplicate($this->getMainTable(), $data, array('content', 'upload_time'));

        return $this;
    }

    /**
     * Rename files in database
     *
     * @param  string $oldFilename
     * @param  string $oldPath
     * @param  string $newFilename
     * @param  string $newPath
     * @return Magento_Core_Model_Resource_File_Storage_Database
     */
    public function renameFile($oldFilename, $oldPath, $newFilename, $newPath)
    {
        $adapter    = $this->_getWriteAdapter();
        $dataUpdate = array('filename' => $newFilename, 'directory' => $newPath);

        $dataWhere  = array('filename = ?' => $oldFilename);
        $dataWhere[] = new Zend_Db_Expr($adapter->prepareSqlCondition('directory', array('seq' => $oldPath)));

        $adapter->update($this->getMainTable(), $dataUpdate, $dataWhere);

        return $this;
    }

    /**
     * Copy files in database
     *
     * @param  string $oldFilename
     * @param  string $oldPath
     * @param  string $newFilename
     * @param  string $newPath
     * @return Magento_Core_Model_Resource_File_Storage_Database
     */
    public function copyFile($oldFilename, $oldPath, $newFilename, $newPath)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('filename = ?', $oldFilename)
            ->where($adapter->prepareSqlCondition('directory', array('seq' => $oldPath)));

        $data = $adapter->fetchRow($select);
        if (!$data) {
            return $this;
        }

        if (isset($data['file_id']) && isset($data['filename'])) {
            unset($data['file_id']);
            $data['filename'] = $newFilename;
            $data['directory'] = $newPath;

            $writeAdapter = $this->_getWriteAdapter();
            $writeAdapter->insertOnDuplicate($this->getMainTable(), $data, array('content', 'upload_time'));
        }

        return $this;
    }

    /**
     * Check whether file exists in DB
     *
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public function fileExists($filename, $path)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('filename = ?', $filename)
            ->where($adapter->prepareSqlCondition('directory', array('seq' => $path)))
            ->limit(1);

        $data = $adapter->fetchRow($select);
        return (bool)$data;
    }

    /**
     * Delete files that starts with given $folderName
     *
     * @param string $folderName
     */
    public function deleteFolder($folderName = '')
    {
        $folderName = rtrim($folderName, '/');
        if (!strlen($folderName)) {
            return;
        }

        /* @var $resHelper Magento_Core_Model_Resource_Helper_Abstract */
        $resHelper = Mage::getResourceHelper('Magento_Core');
        $likeExpression = $resHelper->addLikeEscape($folderName . '/', array('position' => 'start'));
        $this->_getWriteAdapter()
            ->delete($this->getMainTable(), new Zend_Db_Expr('filename LIKE ' . $likeExpression));
    }

    /**
     * Delete file
     *
     * @param string $filename
     * @param string $directory
     */
    public function deleteFile($filename, $directory)
    {
        $adapter = $this->_getWriteAdapter();

        $where = array('filename = ?' => $filename);
        $where[] = new Zend_Db_Expr($adapter->prepareSqlCondition('directory', array('seq' => $directory)));

        $adapter->delete($this->getMainTable(), $where);
    }

    /**
     * Return directory file listing
     *
     * @param string $directory
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
        $directory = trim($directory, '/');
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array(
                    'filename',
                    'directory',
                    'content'
                )
            )
            ->where($adapter->prepareSqlCondition('directory', array('seq' => $directory)))
            ->order('file_id');

        $rows = $adapter->fetchAll($select);
        return $this->_decodeAllFilesContent($rows);
    }
}
