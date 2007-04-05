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
    public function __construct($customer=false) 
    {
        if (is_array($customer)) {
            parent::__construct($customer);
        }
        elseif ($customer) {
            $this->load((int)$address);
        }
        else {
            parent::__construct();
        }
    }
    
    public function load($customerId)
    {
        $this->_customerId = $customerId;
        $this->_data = Mage::getResourceModel('customer', 'customer')->getRow($customerId);
    }
    
    public function getDefaultAddress()
    {
        return $this->getAddress($this->defaultAddressId);
    }
    
    public function getAddress($addressId)
    {
        return new Mage_Customer_Address($addressId);
    }
}