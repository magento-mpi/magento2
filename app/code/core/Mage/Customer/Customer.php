<?php
/**
 * Customer
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Customer extends Varien_Data_Object
{
    public function __construct($customer=false) 
    {
        parent::__construct();
        
        if (is_numeric($customer)) {
            $this->load($customer);
        } elseif (is_array($customer)) {
            $this->setData($customer);
        }
    }
    
    public function load($customerId)
    {
        $data = Mage::getResourceModel('customer', 'customer')->getRow($customerId);
        if ($data) {
            $this->setData($data);
        }
    }
    
    public function validateCreate()
    {
        return true;
    }
    
    public function validateUpdate()
    {
        return true;
    }
    
    public function validateChangePassword()
    {
        return true;
    }
    
    public function getAddress($addressId)
    {
        return new Mage_Customer_Address($addressId);
    }
}