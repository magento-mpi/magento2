<?php
/**
 * Customer address
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Address extends Varien_Data_Object 
{
    /**
     * Constructor receives $address as array of fields for new address or integer to load existing id
     *
     * @param array|integer $address
     */
    public function __construct($address=false) 
    {
        if (is_array($address)) {
            parent::__construct($address);
            $this->_explodeStreetAddress();
        }
        elseif (is_numeric($address)) {
            $this->load($address);
        }
        else {
            parent::__construct();
        }
    }
    
    public function setAndValidate($data)
    {
        
    }
    
    public function load($addressId)
    {
        if ($this->_data = Mage::getResourceModel('customer', 'address')->getRow($addressId)) {
            $this->addressId = $addressId;
            $this->explodeStreetAddress();
        }
        else {
            $this->_data = array();
        }
    }
    
    public function explodeStreetAddress()
    {
        if ($this->getStreet()) {
            if (is_array($this->getStreet())) {
                $street = $this->getStreet();
                foreach ($street as $index => $value) {
                    $this->setData('street' . ($index+1), $value);
                }
            }
            else {
                // Set street{$index} fields
                $arrStreet = explode("\n", $this->getStreet());
                foreach ($arrStreet as $index => $value) {
                    $this->setData('street' . ($index+1), $value);
                }
            }
        }
    }
    
    public function combineStreetAddress()
    {
        if ($this->getStreet() && is_array($this->getStreet())) {
            $this->setStreet(join("\n", $this->getStreet()));
        }
    }
    
    public function save()
    {
        $this->_combineStreetAddress();
        
        $addressModel = Mage::getResourceModel('customer', 'address');
        
        if (!$this->getAddressId()) {
            $addressModel->update($this->_data, $this->getAddressId());
        } else {
            $this->setAddressId($addressModel->insert($this->_data));
        }
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