<?php
/**
 * Customer auth
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Auth
{
    public static function load_front_action_preDispatch()
    {
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('var').DS.'session'));
        Zend_Session::start();
        
        Mage::register('AUTH', $auth = new Zend_Session_Namespace('Mage_Customer'));

        if (empty($auth->user) && isset($_POST['login'])) {
            extract($_POST['login']);
            if (!empty($customer_login) && !empty($customer_pass)) {
                $auth->customer = Mage::getModel('customer', 'customer')->authenticate($customer_login, $customer_pass);
            }
        }
    }
}