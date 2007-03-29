<?php
/**
 * Customer data validation class
 * 
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Validate_Customer extends Mage_Core_Validate 
{
    /**
     * Data validation
     */
    public function createAccount($data) 
    {
        $arrData= $this->_prepareArray($data, array('firstname', 'lastname', 'email', 'password'));
        
        $this->_data = array();
        $this->_data['customer_email']      = $arrData['email'];
        $this->_data['customer_pass']       = $arrData['password'];
        $this->_data['customer_firstname']  = $arrData['firstname'];
        $this->_data['customer_lastname']   = $arrData['lastname'];
        $this->_data['customer_type_id']    = 1; // TODO: default or defined customer type
        return true;
    }

    public function changePassword($data)
    {
        if (!isset($data['current_password'])) {
            $this->_message = 'Current customer password is empty';
            return false;
        }
        else {
            $customerModel = Mage::getModel('customer', 'customer');
            
            if (!$customerModel->checkPassword(Mage_Customer_Front::getCustomerId(), $data['current_password'])) {
                $this->_message = 'Invalid current password';
                return false;
            }
            if (empty($data['password'])) {
                return false;
            }
        }
        
        $this->_data['password'] = $data['password'];
        return true;
    }
}