<?php
/**
 * Customer
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Customer extends Varien_DataObject
{
    protected $_customerId;
    
    public function __construct($customerId=false) 
    {
        if ($customerId) {
            $this->_customerId = $customerId;
            $this->load($customerId);
        }
    }
    
    public function load($customerId)
    {
        $this->_customerId = $customerId;
        $this->_data = array();
    }
}