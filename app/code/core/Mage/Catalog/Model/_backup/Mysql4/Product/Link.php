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
        $this->_linkTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_link');
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function load($rowId)
    {
        return $this->_read->fetchRow("SELECT * FROM $this->_linkTable WHERE link_id=?", $rowId);
    }  
    
    public function save($link)
    {
        if (!$link->getLinkId()) {
            if ($this->_write->insert($this->_linkTable, $link->getData())) {
                $link->setLinkId($this->_write->lastInsertId());
            }
        } else {
            $condition = $this->_write->quoteInto('link_id=?', $link->getLinkId());
            $this->_write->update($this->_linkTable, $link->getData(), $condition);
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
        $condition = $this->_write->quoteInto('link_id=?', $rowId);
        return $this->_write->delete($this->_linkTable, $condition);
    }
}