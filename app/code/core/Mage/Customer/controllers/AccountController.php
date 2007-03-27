<?php
/**
 * Customer account controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        if (Mage::registry('AUTH') && Mage::registry('AUTH')->customer) {
            $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/card/');
            return;
        }
        
        $block = Mage::createBlock('customer_login', 'customer.login');
        Mage::getBlock('content')->append($block);
    }
    
    public function logoutAction()
    {
        Mage_Customer_Front::logout();
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
        //$this->_forward('index');
    }
    
    /**
     * Registration form
     *
     */
    public function createAction()
    {
        $block = Mage::createBlock('customer_regform', 'customer.regform');
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Account information page
     *
     */
    public function cardAction()
    {
        // TODO: auth check
        $block = Mage::createBlock('customer_account', 'customer.account');
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Edit base iformation form
     *
     */
    public function editAction()
    {
        
    }
    
    /**
     * Change password form
     *
     */
    public function passwordAction()
    {
        
    }
    
    /**
     * Address book
     *
     */
    public function addressBookAction()
    {
        
    }
    
    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $block = Mage::createBlock('tpl', 'customer.forgotpassword')
            ->setViewName('Mage_Customer', 'form/forgotpassword');
        Mage::getBlock('content')->append($block);
    }
}// Class Mage_Customer_AccountController END