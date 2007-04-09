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
     * Validate data for account create
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
        
        $customerModel = Mage::getModel('customer', 'customer');
        $customer = $customerModel->loadByEmail($arrData['email']);
        if ($customer->getCustomerId()) {
            $this->_message = 'Your E-Mail Address already exists in our records - please log in with the e-mail address or create an account with a different address';
            return false;
        }
        return true;
    }
    
    /**
     * Validate data for change account information
     *
     */
    public function editAccount($data)
    {
        $arrData= $this->_prepareArray($data, array('customer_firstname', 'customer_lastname', 'customer_email'));
        $this->_data = $arrData;
        // validate fields.....
        
        // Validate email
        $customerModel = Mage::getModel('customer', 'customer');
        $customer = $customerModel->loadByEmail($arrData['customer_email']);

        if ($customer->getCustomerId() && ($customer->getCustomerId() != Mage_Customer_Front::getCustomerId())) {
            $this->_message = 'E-Mail Address already exists';
            return false;
        }

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