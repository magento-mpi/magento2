<?php
/**
 * Customer address collection
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Address_Collection extends Mage_Core_Resource_Model_Collection
{
    protected $_addressTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('customer'));
        $this->_addressTable    = $this->_dbModel->getTableName('customer', 'customer_address');
        $this->_sqlSelect->from($this->_addressTable);
        
        $this->setItemObjectClass('Mage_Customer_Address');
    }
}