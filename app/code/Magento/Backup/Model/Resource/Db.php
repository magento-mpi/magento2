<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Database backup resource model
 *
 * @category    Magento
 * @package     Magento_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Model\Resource;

class Db
{
    /**
     * Database connection adapter
     *
     * @var \Magento\DB\Adapter\Pdo\Mysql
     */
    protected $_write;

    /**
     * tables Foreign key data array
     * [tbl_name] = array(create foreign key strings)
     *
     * @var array
     */
    protected $_foreignKeys    = array();

    /**
     * Initialize Backup DB resource model
     *
     */
    public function __construct()
    {
        $this->_write = \Mage::getSingleton('Magento\Core\Model\Resource')->getConnection('backup_write');
    }

    /**
     * Clear data
     *
     */
    public function clear()
    {
        $this->_foreignKeys = array();
    }

    /**
     * Retrieve table list
     *
     * @return array
     */
    public function getTables()
    {
        return $this->_write->listTables();
    }

    /**
     * Retrieve SQL fragment for drop table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDropSql($tableName)
    {
        return \Mage::getResourceHelper('Magento_Backup')->getTableDropSql($tableName);
    }

    /**
     * Retrieve SQL fragment for create table
     *
     * @param string $tableName
     * @param bool $withForeignKeys
     * @return string
     */
    public function getTableCreateSql($tableName, $withForeignKeys = false)
    {
        return \Mage::getResourceHelper('Magento_Backup')->getTableCreateSql($tableName, $withForeignKeys = false);
    }

    /**
     * Retrieve foreign keys for table(s)
     *
     * @param string|null $tableName
     * @return string
     */
    public function getTableForeignKeysSql($tableName = null)
    {
        $fkScript = '';
        if (!$tableName) {
            $tables = $this->getTables();
            foreach($tables as $table) {
                $tableFkScript = \Mage::getResourceHelper('Magento_Backup')->getTableForeignKeysSql($table);
                if (!empty($tableFkScript)) {
                    $fkScript .= "\n" . $tableFkScript;
                }
            }
        } else {
            $fkScript = $this->getTableForeignKeysSql($tableName);
        }
        return $fkScript;
    }

    /**
     * Retrieve table status
     *
     * @param string $tableName
     * @return \Magento\Object
     */
    public function getTableStatus($tableName)
    {
        $row = $this->_write->showTableStatus($tableName);

        if ($row) {
            $statusObject = new \Magento\Object();
            $statusObject->setIdFieldName('name');
            foreach ($row as $field => $value) {
                $statusObject->setData(strtolower($field), $value);
            }

            $cntRow = $this->_write->fetchRow(
                    $this->_write->select()->from($tableName, 'COUNT(1) as rows'));
            $statusObject->setRows($cntRow['rows']);

            return $statusObject;
        }

        return false;
    }

    /**
     * Retrive table partical data SQL insert
     *
     * @param string $tableName
     * @param int $count
     * @param int $offset
     * @return string
     */
    public function getTableDataSql($tableName, $count = null, $offset = null)
    {
        return \Mage::getResourceHelper('Magento_Backup')->getPartInsertSql($tableName, $count, $offset);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $tableName
     * @param unknown_type $addDropIfExists
     * @return unknown
     */
    public function getTableCreateScript($tableName, $addDropIfExists = false)
    {
        return \Mage::getResourceHelper('Magento_Backup')->getTableCreateScript($tableName, $addDropIfExists);;
    }

    /**
     * Retrieve table header comment
     *
     * @param unknown_type $tableName
     * @return string
     */
    public function getTableHeader($tableName)
    {
        $quotedTableName = $this->_write->quoteIdentifier($tableName);
        return "\n--\n"
            . "-- Table structure for table {$quotedTableName}\n"
            . "--\n\n";
    }

    /**
     * Return table data dump
     *
     * @param string $tableName
     * @param bool $step
     * @return string
     */
    public function getTableDataDump($tableName, $step = false)
    {
        return $this->getTableDataSql($tableName);
    }

    /**
     * Returns SQL header data
     *
     * @return string
     */
    public function getHeader()
    {
        return \Mage::getResourceHelper('Magento_Backup')->getHeader();
    }

    /**
     * Returns SQL footer data
     *
     * @return string
     */
    public function getFooter()
    {
        return \Mage::getResourceHelper('Magento_Backup')->getFooter();
    }

    /**
     * Retrieve before insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataBeforeSql($tableName)
    {
        return \Mage::getResourceHelper('Magento_Backup')->getTableDataBeforeSql($tableName);
    }

    /**
     * Retrieve after insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataAfterSql($tableName)
    {
        return \Mage::getResourceHelper('Magento_Backup')->getTableDataAfterSql($tableName);
    }

    /**
     * Start transaction mode
     *
     * @return \Magento\Backup\Model\Resource\Db
     */
    public function beginTransaction()
    {
        \Mage::getResourceHelper('Magento_Backup')->turnOnSerializableMode();
        $this->_write->beginTransaction();
        return $this;
    }

    /**
     * Commit transaction
     *
     * @return \Magento\Backup\Model\Resource\Db
     */
    public function commitTransaction()
    {
        $this->_write->commit();
        \Mage::getResourceHelper('Magento_Backup')->turnOnReadCommittedMode();
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @return \Magento\Backup\Model\Resource\Db
     */
    public function rollBackTransaction()
    {
        $this->_write->rollBack();
        return $this;
    }

    /**
     * Run sql code
     *
     * @param $command
     * @return \Magento\Backup\Model\Resource\Db
     */
    public function runCommand($command){
        $this->_write->query($command);
        return $this;
    }
}
