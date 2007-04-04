<?php
/**
 * Customer address
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Address
{
    protected $_addressId;
    
    public function __construct($addressId=false) 
    {
        if ($addressId) {
            $this->_addressId = $addressId;
            $this->load($addressId);
        }
    }
    
    public function load($addressId)
    {
        $this->_addressId = $addressId;
    }
}