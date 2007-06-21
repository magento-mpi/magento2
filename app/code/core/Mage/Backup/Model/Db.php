<?php
/**
 * Database backup model
 *
 * @package     Mage
 * @subpackage  Backup
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Backup_Model_Db
{
    public function __construct() 
    {
        
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('backup/db');
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
    
    public function renderSql()
    {
        $sql = '';
        
        $tables = $this->getTables();
        foreach ($tables as $tableName) {
        	$sql.= $this->getTableCreateScript($tableName, true);
        	$sql.= $this->getTableDataDump($tableName);
        }
        return $sql;
    }
}
