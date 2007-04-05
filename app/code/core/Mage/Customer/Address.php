<?php
/**
 * Customer address
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Address extends Varien_DataObject 
{
    public function __construct($address=false) 
    {
        if (is_array($address)) {
            parent::__construct($address);
            $this->_explodeStreetAddress();
        }
        elseif (is_int($address) && $address) {
            $this->load($address);
        }
        else {
            parent::__construct();
        }
    }
    
    public function load($addressId)
    {
        if($this->_data = Mage::getResourceModel('customer', 'address')->getRow($addressId)) {
            $this->addressId = $addressId;
            $this->_explodeStreetAddress();
        }
        else {
            $this->_data = array();
        }
    }
    
    protected function _explodeStreetAddress()
    {
        if ($this->street) {
            if (is_array($this->street)) {
                $street = $this->street;
                foreach ($street as $index => $value) {
                    $this->setData('street' . ($index+1), $value);
                }
            }
            else {
                // Set street{$index} fields
                $arrStreet = explode("\n", $this->street);
                foreach ($arrStreet as $index => $value) {
                    $this->setData('street' . ($index+1), $value);
                }
            }
        }
    }
    
    public function save()
    {
        
    }
    
    public function toString($format='')
    {
        if (empty($format)) {
            return implode(', ', $this->_data);
        }
        return '// TODO: address string format';
    }
    
    public function hasCustomer($customerId)
    {
        return true;
    }
}