<?php
/**
 * Customer
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Customer_Customer extends Varien_Data_Object
{
    public function __construct($customer=false) 
    {
        parent::__construct();
        
        if (is_numeric($customer)) {
            $this->getByCustomerId($customer);
        } elseif (is_array($customer)) {
            $this->setData($customer);
        }
    }
    
    public function load($customerId)
    {
        $this->getByCustomerId($customerId);
    }
    
    public function validateCreate()
    {
        return true;
    }
    
    public function validateUpdate()
    {
        return true;
    }
    
    public function validateChangePassword($data)
    {
        return true;
    }
    
    public function getAddress($addressId)
    {
        $address = Mage::getConfig()->getResourceModelClassName('customer', 'address');
        $address->getByAddressId($addressId);
        return $address;
    }
}