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
        parent::__construct(Mage::registry('resources')->getConnection('customer_read'));
        $this->_customerTable = Mage::registry('resources')->getTableName('customer_resource', 'customer');
        $this->_sqlSelect->from($this->_customerTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('customer/customer'));
    }
}