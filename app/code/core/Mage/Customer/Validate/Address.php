<?php
/**
 * Customer address data validation class
 * 
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Validate_Address extends Mage_Core_Validate 
{
    /**
     * Data validation
     */
    public function isValid() 
    {
        $arrData= $this->_prepareArray($this->_data, 
            array(
                'firstname', 
                'lastname', 
                'company',
                'street',
                'city',
                'region_id',
                'postcode',
                'country_id',
                'telephone',
                'fax',
            )
        );
        
        if (is_array($arrData['street'])) {
            $arrData['street'] = implode("\n", $arrData['street']);
        }
        
        $this->_data = $arrData;
        return true;
    }
    
    public function hasCustomer($addressId, $customerId)
    {
        // TODO: check in DB
        return true;
    }
}