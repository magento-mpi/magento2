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
 * Product attributes group collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group_Collection extends Varien_Data_Collection_Db
{
    protected $_groupTable;
    protected $_setTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        $this->_groupTable  = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_group');
        $this->_setTable    = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_set');
        
        $this->_sqlSelect->from($this->_groupTable);
        $this->_sqlSelect->join($this->_setTable, "$this->_groupTable.set_id=$this->_setTable.set_id", 'set_id');
        $this->setOrder($this->_groupTable.'.position', 'asc');
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_attribute_group'));
    }
    
    public function addSetFilter($setId)
    {
        $this->addFilter('set', $this->_conn->quoteInto($this->_groupTable.'.set_id=?', $setId), 'string');
        return $this;
    }
}
