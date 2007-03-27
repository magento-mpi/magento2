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
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('var').DS.'session'));
        Zend_Session::start();
        
        Mage::register('AUTH', $auth = new Zend_Session_Namespace('Mage_Customer'));
        
        // Login
        if (empty($auth->customer) && isset($_POST['login'])) {
            extract($_POST['login']);
            if (!empty($customer_email) && !empty($customer_pass)) {
                $auth->customer = Mage::getModel('customer', 'customer')->authenticate($customer_email, $customer_pass);
            }
        }
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
}