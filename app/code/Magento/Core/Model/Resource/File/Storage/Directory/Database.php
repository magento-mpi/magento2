<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\File\Storage\Directory;

/**
 * Class Database
 */
class Database extends \Magento\Core\Model\Resource\File\Storage\AbstractStorage
{
    /**
     * Define table name and id field for resource
     */
    protected function _construct()
    {
        $this->_init('core_directory_storage', 'directory_id');
    }

    /**
     * Create database scheme for storing files
     *
     * @return $this
     */
    public function createDatabaseScheme()
    {
        $adapter = $this->_getWriteAdapter();
        $table = $this->getMainTable();
        if ($adapter->isTableExists($table)) {
            return $this;
        }

        $ddlTable = $adapter->newTable($table)
            ->addColumn('directory_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'Directory Id')
            ->addColumn('name', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
                'nullable' => false
            ), 'Directory Name')
            ->addColumn('path', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'default' => null), 'Path to the \Directory')
            ->addColumn('upload_time', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
                'nullable' => false,
                'default' => \Magento\DB\Ddl\Table::TIMESTAMP_INIT
                ), 'Upload Timestamp')
            ->addColumn('parent_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'nullable' => true,
                'default' => null,
                'unsigned' => true
                ), 'Parent \Directory Id')
            ->addIndex($adapter->getIndexName($table, array('name', 'parent_id'),
                \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('name', 'parent_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($adapter->getIndexName($table, array('parent_id')), array('parent_id'))
            ->addForeignKey($adapter->getForeignKeyName($table, 'parent_id', $table, 'directory_id'),
                'parent_id', $table, 'directory_id',
                \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Directory Storage');

        $adapter->createTable($ddlTable);
        return $this;
    }

    /**
     * Load entity by path
     *
     * @param  \Magento\Core\Model\File\Storage\Directory\Database $object
     * @param  string $path
     * @return $this
     */
    public function loadByPath(\Magento\Core\Model\File\Storage\Directory\Database $object, $path)
    {
        $adapter = $this->_getReadAdapter();

        $name = basename($path);
        $path = dirname($path);
        if ($path == '.') {
            $path = '';
        }

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()))
            ->where('name = ?', $name)
            ->where($adapter->prepareSqlCondition('path', array('seq' => $path)));

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * Return parent id
     *
     * @param string $path
     * @return int
     */
    public function getParentId($path)
    {
        $adapter = $this->_getReadAdapter();

        $name = basename($path);
        $path = dirname($path);
        if ($path == '.') {
            $path = '';
        }

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array('directory_id')
            )
            ->where('name = ?', $name)
            ->where($adapter->prepareSqlCondition('path', array('seq' => $path)));

        return $adapter->fetchOne($select);
    }

    /**
     * Delete all directories from storage
     *
     * @return $this
     */
    public function clearDirectories()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable());

        return $this;
    }

    /**
     * Export directories from database
     *
     * @param int $offset
     * @param int $count
     * @return array
     */
    public function exportDirectories($offset, $count = 100)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array('name', 'path')
            )
            ->order('directory_id')
            ->limit($count, $offset);

        return $adapter->fetchAll($select);
    }

    /**
     * Return directory file listing
     *
     * @param string $directory
     * @return array
     */
    public function getSubdirectories($directory)
    {
        $directory = trim($directory, '/');
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('e' => $this->getMainTable()),
                array('name', 'path')
            )
            ->where($adapter->prepareSqlCondition('path', array('seq' => $directory)))
            ->order('directory_id');

        return $adapter->fetchAll($select);
    }

    /**
     * Delete directory
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    public function deleteDirectory($name, $path)
    {
        $adapter = $this->_getWriteAdapter();

        $where = array('name = ?' => $name);
        $where[] = new \Zend_Db_Expr($adapter->prepareSqlCondition('path', array('seq' => $path)));

        $adapter->delete($this->getMainTable(), $where);
    }
}
