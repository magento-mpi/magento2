<?php
/**
 * Database backup resource model
 *
 * @package     Mage
 * @subpackage  Backup
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Backup_Model_Mysql4_Db
{
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    public function __construct() 
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('backup_read');
    }
    
    public function getTables()
    {
        return $this->_read->listTables();
    }
    
    public function getTableCreateScript($tableName, $addDropIfExists=false)
    {
        $script = '';
        if ($this->_read) {
            if ($addDropIfExists) {
                $script.= $this->_read->quoteInto('DROP TABLE IF EXISTS ?', $tableName).";\n";
            }
            $sql = 'SHOW CREATE TABLE '.$tableName;
            $data = $this->_read->fetchRow($sql);
            $script.= isset($data['Create Table']) ? $data['Create Table']."\n" : '';
        }
        
        return $script;
    }
    
    public function getTableDataDump($tableName, $step=100)
    {
        $sql = '';
        if ($this->_read) {
            $quotedTableName = $this->_read->quoteIdentifier($tableName);
            $colunms = $this->_read->fetchRow('SELECT * FROM '.$quotedTableName.' LIMIT 1');
            if ($colunms) {
                $arrSql = array();
                
                $colunms = array_keys($colunms);
                $quote = $this->_read->getQuoteIdentifierSymbol();
                $sql = 'INSERT INTO ' . $quotedTableName . ' (' .$quote . implode($quote.', '.$quote,$colunms).$quote.')';
                $sql.= ' VALUES ';
                
                $startRow = 0;
                $select = $this->_read->select();
                $select->from($tableName)
                    ->limit($step, $startRow);
                while ($data = $this->_read->fetchAll($select)) {
                    $dataSql = array();
                    foreach ($data as $row) {
                    	$dataSql[] = $this->_read->quoteInto('(?)', $row);
                    }
                    $arrSql[] = $sql.implode(', ', $dataSql).';';
                    $startRow += $step;
                    $select->limit($step, $startRow);
                }
                
                $sql = implode("\n", $arrSql)."\n";
            }
            
        }
        
        return $sql;
    }
    
    /**
     * Returns SQL header data
     */
    public function getHeader()
    {
        $header = "SET NAMES utf8;\n\n"
                . "SET SQL_MODE='';\n\n"
                . "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n"
                . "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        
        return $header;
    }
    
    /**
     * Returns SQL footer data
     */
    public function getFooter()
    {   
        $footer = "SET SQL_MODE=@OLD_SQL_MODE;\n"
                . "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;\n";
        
        return $footer;
    }
}
