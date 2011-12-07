<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Database backup model
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Model_Db
{

    /**
     * Buffer length for multi rows
     * default 512 Kb
     *
     */
    const BUFFER_LENGTH = 524288;

    /**
     * Retrieve resource model
     *
     * @return Mage_Backup_Model_Resource_Db
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('Mage_Backup_Model_Resource_Db');
    }

    public function getTables()
    {
        return $this->getResource()->getTables();
    }

    public function getTableCreateScript($tableName, $addDropIfExists=false)
    {
        return $this->getResource()->getTableCreateScript($tableName, $addDropIfExists);
    }

    public function getTableDataDump($tableName)
    {
        return $this->getResource()->getTableDataDump($tableName);
    }

    public function getHeader()
    {
        return $this->getResource()->getHeader();
    }

    public function getFooter()
    {
        return $this->getResource()->getFooter();
    }

    public function renderSql()
    {
        ini_set('max_execution_time', 0);
        $sql = $this->getHeader();

        $tables = $this->getTables();
        foreach ($tables as $tableName) {
            $sql.= $this->getTableCreateScript($tableName, true);
            $sql.= $this->getTableDataDump($tableName);
        }

        $sql.= $this->getFooter();
        return $sql;
    }

    /**
     * Create backup and stream write to adapter
     *
     * @param Mage_Backup_Model_Backup $backup
     * @return Mage_Backup_Model_Db
     */
    public function createBackup(Mage_Backup_Model_Backup $backup)
    {
        $backup->open(true);

        $this->getResource()->beginTransaction();

        $tables = $this->getResource()->getTables();

        $backup->write($this->getResource()->getHeader());

        foreach ($tables as $table) {
            $backup->write($this->getResource()->getTableHeader($table) . $this->getResource()->getTableDropSql($table) . "\n");
            $backup->write($this->getResource()->getTableCreateSql($table, false) . "\n");

            $tableStatus = $this->getResource()->getTableStatus($table);

            if ($tableStatus->getRows()) {
                $backup->write($this->getResource()->getTableDataBeforeSql($table));

                if ($tableStatus->getDataLength() > self::BUFFER_LENGTH) {
                    if ($tableStatus->getAvgRowLength() < self::BUFFER_LENGTH) {
                        $limit = floor(self::BUFFER_LENGTH / $tableStatus->getAvgRowLength());
                        $multiRowsLength = ceil($tableStatus->getRows() / $limit);
                    }
                    else {
                        $limit = 1;
                        $multiRowsLength = $tableStatus->getRows();
                    }
                }
                else {
                    $limit = $tableStatus->getRows();
                    $multiRowsLength = 1;
                }

                for ($i = 0; $i < $multiRowsLength; $i ++) {
                    $backup->write($this->getResource()->getTableDataSql($table, $limit, $i*$limit));
                }

                $backup->write($this->getResource()->getTableDataAfterSql($table));
            }
        }
        $backup->write($this->getResource()->getTableForeignKeysSql());
        $backup->write($this->getResource()->getFooter());

        $this->getResource()->commitTransaction();

        $backup->close();

        return $this;
    }

}
