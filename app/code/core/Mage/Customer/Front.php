<?php
/**
 * Customer front
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Front
{
    public static function load_front_action_preDispatch()
    {
        Mage::register('AUTH', $auth = new Zend_Session_Namespace('Mage_Customer'));
    }
    
    public static function load_front_action_postDispatch()
    {
        // Add logout link
        if (Mage::registry('AUTH')->customer) {
            Mage::getBlock('top.links')->append(
                Mage::createBlock('list_link', 'top.links.logout')->setLink(
                    '', 'href="'.Mage::getBaseUrl('','Mage_Customer').'/account/logout/"', 'Logout', ''
                )
            );
        }
    }
    
    public static function logout()
    {
        if (Mage::registry('AUTH')->customer) {
            unset(Mage::registry('AUTH')->customer);
        }
    }
    
    public static function login($login, $password)
    {
        Mage::registry('AUTH')->customer = Mage::getModel('customer', 'customer')->authenticate($login, $password);
        if (Mage::registry('AUTH')->customer) {
            Mage::dispatchEvent('customer_login');
            return true;
        }
        return false;
    }
    
    public static function authenticate()
    {
        $auth = Mage::registry('AUTH');
        
        // Login
        if (empty($auth->customer)) {
            if (isset($_POST['login'])) {
                extract($_POST['login']);
                if (!empty($customer_email) && !empty($customer_pass)) {
                    if (self::login($customer_email, $customer_pass)) {
                        return true;
                    }
                }
            }
            return false;
        }
        return true;
    }
    
    /**
     * Get authenticated customer id 
     *
     * @return int || null
     */
    public static function getCustomerId()
    {
        return self::getCustomerInfo('customer_id');
    }
    
    /**
     * Get authenticated customer field
     *
     * @param   string $fieldName
     * @return  null
     */
    public static function getCustomerInfo($fieldName = '')
    {
        if (Mage::registry('AUTH')->customer) {
            if (empty($fieldName)) {
                return Mage::registry('AUTH')->customer;
            }
            else {
                return isset(Mage::registry('AUTH')->customer->$fieldName) ? Mage::registry('AUTH')->customer->$fieldName : null;
            }
        }
        return false;
    }
    
    /**
     * Set authenticated customer field value
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     */
    public static function setCustomerInfo($fieldName, $fieldValue)
    {
        if (Mage::registry('AUTH')->customer) {
            if ($fieldName!='customer_id') {
                Mage::registry('AUTH')->customer->$fieldName = $fieldValue;
                return true;
            }            
        }
        return false;
    }
}