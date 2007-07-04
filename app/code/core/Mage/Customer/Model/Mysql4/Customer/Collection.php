<?php
/**
 * Customers collection
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Customer_Collection extends Varien_Data_Collection_Db 
{
    protected $_customerTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('customer_read'));
        $this->_customerTable = Mage::getSingleton('core/resource')->getTableName('customer/customer');
        $this->_sqlSelect->from($this->_customerTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('customer/customer'));
    }
    
    public function setNameFilter($q) {
    	$this->_sqlSelect->where("firstname LIKE ? OR lastname LIKE ?", "%$q%');
    	
    	return $this;
    }
}