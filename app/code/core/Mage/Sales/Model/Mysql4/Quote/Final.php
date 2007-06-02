<?php

/**
 * Final quote mysql4 resource model
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Mysql4_Quote_Final extends Mage_Sales_Model_Mysql4_Quote
{
    /**
     * Initialize the resource if needed and set doc type
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initResource();
        $this->setDocType('quote_final');
    }
    
    /**
     * Initialize the resource
     * 
     * Creates quote tables in memory based on real quote tables
     *
     * @return Mage_Sales_Model_Mysql4_Quote_Final
     */
    public function initResource()
    {
        $tablesExist = (bool)$this->_read->fetchOne("show tables like '{$this->_documentTable}_final'");
        if ($tablesExist) {
            return $this;
        }
        
        $origTables = array($this->_documentTable);
        foreach (array('datetime', 'decimal', 'int', 'text', 'varchar') as $attributeType) {
            $origTables[] = $this->_attributeTable.'_'.$attributeType;
        }
        
        foreach ($origTables as $tableName) {
            $row = $this->_read->fetchRow("show create table `$tableName`");
            $sql = $row['Create Table'];
            $sql = preg_replace('#^create table `'.$tableName.'#i', 'create table `'.$tableName.'_final', $sql);
            $sql = preg_replace('#\bengine=([a-z]+)#i', 'engine=memory', $sql);
            $this->_write->query($sql);
        }
        return $this;
    }
}