<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product link model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
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