<?php
/**
 * Product link model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Link extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_linkTable;
    
    public function __construct() 
    {
        $this->_linkTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_link');
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function load($rowId)
    {
        return self::$_read->fetchRow("SELECT * FROM $this->_linkTable WHERE link_id=?", $rowId);
    }  
    
    public function save($link)
    {
        if (!$link->getLinkId()) {
            if (self::$_write->insert($this->_linkTable, $link->getData())) {
                $link->setLinkId(self::$_write->lastInsertId());
            }
        } else {
            $condition = self::$_write->quoteInto('link_id=?', $link->getLinkId());
            self::$_write->update($this->_linkTable, $link->getData(), $condition);
        }
        return $this;
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = self::$_write->quoteInto('link_id=?', $rowId);
        return self::$_write->delete($this->_linkTable, $condition);
    }
}