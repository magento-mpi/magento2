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
    public function preDispatch()
    {
        parent::preDispatch();
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^create#', $action)) {
            if (!Mage_Customer_Front::authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }
    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        $block = Mage::createBlock('customer_account', 'customer.account');
        Mage::getBlock('content')->append($block);
    }
    
    public function loginAction()
    {

    }
    
    public function logoutAction()
    {
        Mage_Customer_Front::logout();
        $this->_redirect(Mage::getBaseUrl());
    }
    
    /**
     * Registration form
     *
     */
    public function createAction()
    {
        if (!empty($_POST)) {
            $customerValidator = new Mage_Customer_Validate_Customer($_POST);
            $addressValidator = new Mage_Customer_Validate_Address($_POST);
            
            // Validate customer and address info
            if ($customerValidator->isValid() && $addressValidator->isValid()) {
                
                $customerModel = Mage::getModel('customer', 'customer');
                
                // Insert customer information
                $customerData = $customerValidator->getData();
                if ($customerId = $customerModel->insert($customerData)) {
                    
                    $addressModel = Mage::getModel('customer', 'address');
                    
                    // Insert customer address
                    $addressData = $addressValidator->getData();
                    $addressData['customer_id'] = $customerId;
                    if ($addressId = $addressModel->insert($addressData)) {
                        
                        $customerModel->setDefaultAddress($customerId, $addressId);
                        Mage_Customer_Front::login($customerData['customer_email'], $customerData['customer_pass']);
                        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
                    }
                    else {
                        // Delete customer? and can't create address error
                    }
                }
                else {
                    // Can't create customer
                }
            }
            else {
                // Fix validation error
            }
        }
        $block = Mage::createBlock('customer_regform', 'customer.regform');
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $block = Mage::createBlock('tpl', 'customer.changepassword')
            ->setViewName('Mage_Customer', 'form/changepassword');
        Mage::getBlock('content')->append($block);
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

    public function newsletterAction()
    {
        $block = Mage::createBlock('tpl', 'customer.newsletter')
            ->setViewName('Mage_Customer', 'form/newsletter');
        Mage::getBlock('content')->append($block);
    }
}// Class Mage_Customer_AccountController END