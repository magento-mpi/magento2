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
        if (!preg_match('#^[create|forgotpassword]#', $action)) {
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
        $block = Mage::createBlock('customer_regform', 'customer.regform')
            ->assign('action', Mage::getBaseUrl('', 'Mage_Customer') . '/account/createPost/');
        Mage::getBlock('content')->append($block);
    }
    
    public function createPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer')->setData($_POST);
            $address = Mage::getModel('customer', 'address')->setData($_POST);
            
            // Validate customer and address info
            if ($customer->validate() && $address->validate()) {
                
                if ($customer->save()) {
                    $address->setCustomerId($customer->getCustomerId());
                    
                    if ($address->save()) {
                        $customer->setPrimaryAddress($address->getAddressId());
                        Mage_Customer_Front::login($customer->getCustomerEmail(), $customer->getCustomerPass());
                        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
                    } else {
                        // Delete customer? and can't create address error
                    }
                } else {
                    // Can't create customer
                }
            } else {
                // Fix validation error and tmp save post data
            }
        }
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/create/');
    }
    
    public function editAction()
    {
        $formData = Mage::registry('session')->getNamespaceData('customer_edit');
        if ($formData->isEmpty()) {
            $customerModel = Mage::getModel('customer', 'customer');
            $formData = $customerModel->load(Mage_Customer_Front::getCustomerId());
        }
        
        $message = Mage::createBlock('message', 'customer.edit.message');
        $message->setMessage(Mage::registry('session')->getNamespaceMessage('customer_edit'));

        $block = Mage::createBlock('tpl', 'customer.edit')
            ->setViewName('Mage_Customer', 'form/edit.phtml')
            ->assign('formData', $formData)
            ->assign('action', Mage::getBaseUrl('', 'Mage_Customer').'/account/editPost/')
            ->setChild('message', $message);
            
        Mage::getBlock('content')->append($block);
    }
    
    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer')->setData($_POST);
            
            if ($customer->validate()) {
                $customer->setCustomerId(Mage_Customer_Front::getCustomerId());
                if ($customer->save()) {
                    
                } else {
                    // problem saving customer
                }
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/account/');
                return;
            }

            Mage::registry('session')
                ->getNamespaceMessage('customer_edit', false)
                    ->addMessage($customer->getMessage(), 'error');

            Mage::registry('session')
                ->getNamespaceData('customer_edit', false)
                    ->setData($customerValidator->getData());
        }
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/account/edit/');
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $block = Mage::createBlock('tpl', 'customer.changepassword')
            ->setViewName('Mage_Customer', 'form/changepassword.phtml')
            ->assign('action', Mage::getBaseUrl('', 'Mage_Customer').'/account/changePasswordPost/');
            
        Mage::getBlock('content')->append($block);
    }
    
    public function changePasswordPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customerValidator = new Mage_Customer_Validate_Customer();
            
            if ($customerValidator->changePassword($_POST)) {
                $customerModel = Mage::getModel('customer', 'customer');
                $customerModel->changePassword(Mage_Customer_Front::getCustomerId(), $customerValidator->getDataItem('password'));
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/account/');
            }
            else {
                // TODO: register error message
            }
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/account/changePassword/');
    }
    
    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $block = Mage::createBlock('tpl', 'customer.forgotpassword')
            ->setViewName('Mage_Customer', 'form/forgotpassword.phtml');
        Mage::getBlock('content')->append($block);
    }
    
    public function forgotPasswordPostAction()
    {
        
    }

    public function newsletterAction()
    {
        $block = Mage::createBlock('tpl', 'customer.newsletter')
            ->setViewName('Mage_Customer', 'form/newsletter.phtml');
        Mage::getBlock('content')->append($block);
    }
    
    public function newsletterPostAction()
    {
        
    }
}// Class Mage_Customer_AccountController END