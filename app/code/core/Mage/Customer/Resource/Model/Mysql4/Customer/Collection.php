<?php
/**
 * Customers collection
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Resource_Model_Mysql4_Customer_Collection extends Mage_Core_Resource_Model_Collection
{
    protected $_customerTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('customer'));
        $this->_customerTable = $this->_dbModel->getTableName('customer_setup', 'customer');
        $this->_sqlSelect->from($this->_customerTable);
    }
}