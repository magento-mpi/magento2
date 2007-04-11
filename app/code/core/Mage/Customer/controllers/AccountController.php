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
            if (!Mage::getSingleton('customer_model', 'session')->authenticate($this)) {
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
        Mage::getSingleton('customer_model', 'session')->logout();
        $this->_redirect(Mage::getBaseUrl());
    }
    
    /**
     * Registration form
     *
     */
    public function createAction()
    {
        $countries = Mage::getModel('directory', 'country_collection');
        $customer = Mage::getSingleton('customer_model', 'session')->getCustomer();
        $messages = Mage::getSingleton('customer_model', 'session')->getMessages(true);

        $block = Mage::createBlock('tpl', 'customer.regform')
            ->setViewName('Mage_Customer', 'form/registration.phtml')
            ->assign('action', Mage::getBaseUrl('', 'Mage_Customer') . '/account/createPost/')
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('regions',     $countries->getDefault()->getRegions())
            ->assign('customer',    $customer)
            ->assign('messages',    $messages);
            
        Mage::getBlock('content')->append($block);
    }
    
    public function createPostAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $address  = Mage::getModel('customer', 'address');
            $address->setData($this->getRequest()->getPost());

            $customer = Mage::getModel('customer', 'customer');
            $customer->setData($this->getRequest()->getPost());

            $customer->addAddress($address);
            
            try {
                $customer->save();
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS001'));
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')->addMessages($e->getMessages());
            }
            catch (Exception $e)
            {
                
            }
/*            $customer   = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());
            $address    = Mage::getModel('customer', 'address')->setData($this->getRequest()->getPost());
            
            // Validate customer and address info
            if ($customer->validate() && $address->validate()) {
                
                if ($customer->save()) {
                    $address->setCustomerId($customer->getCustomerId());
                    
                    if ($address->save()) {
                        
                        $customer->setPrimaryAddress($address->getAddressId());
                        //Mage::getSingleton('customer_model', 'session')->login($customer->getCustomerEmail(), $customer->getCustomerPass());
                        
                        Mage::getSingleton('customer_model', 'session')->setCustomer($customer);
                        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
                    } else {
                        // Delete customer? and can't create address error
                    }
                } else {
                    // Can't create customer
                }
            } else {
                // Fix validation error and tmp save post data
            }*/
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
                $customer->setCustomerId(Mage::getSingleton('customer_model', 'session')->getCustomerId());
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
            $customer = Mage::getSingleton('customer_model', 'session')->getCustomer();
            $currentPassword = $this->getRequest()->getPost('current_password');
            $newPassword = $this->getRequest()->getPost('password');
            
            if ($customer && $customer->validatePassword($currentPassword)) {
                $customer->setCustomerPass($newPassword);
                $customer->save();
                
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