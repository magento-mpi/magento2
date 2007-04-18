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
        if (!preg_match('#^(create|forgotpassword)#', $action)) {
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
        $this->loadLayout();
        
        $block = Mage::createBlock('customer_account', 'customer.account')
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
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
        $this->loadLayout();
        
        // if customer logged in
        if (Mage::getSingleton('customer_model', 'session')->isLoggedIn()) {
            $this->_redirect(Mage::getUrl('customer', array('controller'=>'account')));
        }
        
        $countries = Mage::getModel('directory', 'country_collection');
        $data = Mage::getSingleton('customer_model', 'session')->getCustomerFormData(true);

        $block = Mage::createBlock('tpl', 'customer.regform')
            ->setViewName('Mage_Customer', 'form/registration.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'createPost')))
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('regions',     $countries->getDefault($data->getCountryId())->getRegions())
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    /**
     * Create account
     */
    public function createPostAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $address  = Mage::getModel('customer', 'address')->setData($this->getRequest()->getPost());
            $address->setPrimaryTypes(array_keys($address->getAvailableTypes('address_type_id')));
            $customer = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());

            $customer->addAddress($address);
            
            try {
                $customer->save();
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS001'));
                
                $this->_redirect(Mage::getUrl('customer', array('controller'=>'account')));
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        
        $this->_redirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'create')));
    }
    
    public function editAction()
    {
        $this->loadLayout();
        
        $data = Mage::getSingleton('customer_model', 'session')->getCustomerFormData(true);
        if ($data->isEmpty()) {
            $data = Mage::getSingleton('customer_model', 'session')->getCustomer();
        }
        
        $block = Mage::createBlock('tpl', 'customer.edit')
            ->setViewName('Mage_Customer', 'form/edit.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'editPost')))
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());
            $customer->setCustomerId(Mage::getSingleton('customer_model', 'session')->getCustomerId());
            
            try {
                $customer->save();
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS002'));
                
                $this->_redirect(Mage::getUrl('customer', array('controller'=>'account')));
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        $this->_redirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'edit')));
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $this->loadLayout();
        
        $block = Mage::createBlock('tpl', 'customer.changepassword')
            ->setViewName('Mage_Customer', 'form/changepassword.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'changePasswordPost')))
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function changePasswordPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getSingleton('customer_model', 'session')->getCustomer();
            
            try {
                $customer->changePassword($this->getRequest()->getPost());
                
                Mage::getSingleton('customer_model', 'session')
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS003'));
                
                $this->_redirect(Mage::getUrl('customer', array('controller'=>'account')));
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->addMessages($e->getMessages());
            }
        }
        
        $this->_redirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'changePassword')));
    }
    
    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $this->loadLayout();
        
        $block = Mage::createBlock('tpl', 'customer.forgotpassword')
            ->setViewName('Mage_Customer', 'form/forgotpassword.phtml');
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function forgotPasswordPostAction()
    {
        
    }

    public function newsletterAction()
    {
        $this->loadLayout();
        
        $block = Mage::createBlock('tpl', 'customer.newsletter')
            ->setViewName('Mage_Customer', 'form/newsletter.phtml');
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function newsletterPostAction()
    {
        
    }
}// Class Mage_Customer_AccountController END