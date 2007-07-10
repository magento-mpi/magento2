<?php
/**
 * Customer account controller
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AccountController extends Mage_Core_Controller_Varien_Action
{
    /**
     * Action predispatch
     * 
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^(create|login|forgotpassword|forgotpasswordpost)#', $action)) {
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }
    
    /**
     * Default customer account page
     */
    public function indexAction() 
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('customer/account', 'customer.account')
            ->assign('wishlistActive', Mage::getConfig()->getModuleConfig('Mage_Customer')->is('wishlistActive'))
            ->assign('messages', Mage::getSingleton('customer/session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    /**
     * Customer login form
     */
    public function loginAction()
    {
        $this->loadLayout();
        $this->getLayout()->getMessagesBlock()->setMessages(
            Mage::getSingleton('customer/session')->getMessages(false)
        );
        $block = $this->getLayout()->createBlock('customer/login', 'customer.login')
            ->assign('postAction', Mage::getUrl('customer/account/loginPost', array('_secure'=>true)));
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    /**
     * Login post action
     */
    public function loginPostAction()
    {
        $session = Mage::getSingleton('customer/session');
        
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login)) {
                extract($login);
                if (!empty($username) && !empty($password)) {
                    if (!$session->login($username, $password)) {
                        // _('invalid login or password')
                        $session->addError('Invalid login or password');
                    }
                }
            }
        }
        $this->getResponse()->setRedirect($session->getUrlBeforeAuthentication());
    }
    
    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        Mage::getSingleton('customer/session')->logout();
        $this->getResponse()->setRedirect(Mage::getBaseUrl());
    }
    
    /**
     * Customer register form
     */
    public function createAction()
    {
        $this->loadLayout();
        
        // if customer logged in
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }
        
        $country = Mage::getModel('directory/country');
        $data = Mage::getSingleton('customer/session')->getCustomerFormData(true);
        $data = new Varien_Object($data);

        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/registration.phtml')
            ->assign('action',      Mage::getUrl('customer/account/createPost', array('_secure'=>true)))
            ->assign('countries',   $country->getResourceCollection()->load())
            ->assign('regions',     $country->getRegions())
            ->assign('data',        $data);
            
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $address  = Mage::getModel('customer/address')->setData($this->getRequest()->getPost())
                ->setPostIndex('new');
            
            $customer = Mage::getModel('customer/customer')
                ->setData($this->getRequest()->getPost())
                ->setDefaultBilling('new')
                ->setDefaultShipping('new');

            $customer->addAddress($address);
            
            
            try {
                $customer->save();
                Mage::getSingleton('customer/session')
                    ->setCustomer($customer)
                    // _('customer is registered')
                    ->addSuccess('Customer is registered');

                $mailer = Mage::getModel('customer/email')
                    ->setTemplate('email/welcome.phtml')
                    ->setType('html')
                    ->setCustomer($customer)
                    ->send();
                        
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index', array('_secure'=>true)));
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setCustomerFormData($this->getRequest()->getPost());
            }
        }
        
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/create', array('_secure'=>true)));
    }
    
    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('core/template', 'customer.forgotpassword')
            ->setTemplate('customer/form/forgotpassword.phtml')
            ->assign('action',      Mage::getUrl('customer/account/forgotpasswordpost'))
            ->assign('messages',    Mage::getSingleton('customer/session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function forgotPasswordPostAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            $customer = Mage::getModel('customer/customer')->loadByEmail($email);
            if ($customer->getId()) {
                try {
                    $newPassword = Mage::getModel('core/cookie')->randomSequence(8);
                    $data = array(
                        'password'      => $newPassword,
                        'confirmation'  => $newPassword
                    );
                    
                    $customer->changePassword($data, false)
                        ->setPassword($newPassword);
                        
                    $mailer = Mage::getModel('customer/email')
                        ->setTemplate('email/forgot_password.phtml')
                        ->setCustomer($customer)
                        ->send();
                    
                    Mage::getSingleton('customer/session')
                        ->addMessage(Mage::getModel('customer/message')->success('CSTS006'));
                    
                    $this->_redirect('customer/account');
                    return;
                }
                catch (Exception $e){
                    echo $e;
                }
            }
            else {
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->error('CSTE024'));
               $this->_redirect('customer/account/forgotpassword');
            }
        }
    }


    public function editAction()
    {
        $this->loadLayout();
        
        $data = Mage::getSingleton('customer/session')->getCustomerFormData(true);
        if (!$data || $data->isEmpty()) {
            $data = Mage::getSingleton('customer/session')->getCustomer();
        }
        
        $block = $this->getLayout()->createBlock('core/template', 'customer.edit')
            ->setTemplate('customer/form/edit.phtml')
            ->assign('action',      Mage::getUrl('customer/account/editPost'))
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer/session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')->setData($this->getRequest()->getPost());
            $customer->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            
            try {
                $customer->save();
                Mage::getSingleton('customer/session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer/message')->success('CSTS002'));
                
                $this->_redirect('customer/account');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        $this->_redirect('customer/account/edit');
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('core/template', 'customer.changepassword')
            ->setTemplate('customer/form/changepassword.phtml')
            ->assign('action',      Mage::getUrl('customer/account/changePasswordPost', array('_secure'=>true)))
            ->assign('messages',    Mage::getSingleton('customer/session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function changePasswordPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            
            try {
                $customer->changePassword($this->getRequest()->getPost());
                
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->success('CSTS003'));

                $this->_redirect('customer/account');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addMessages($e->getMessages());
            }
        }
        $this->_redirect('customer/account/changePassword', array('_secure'=>true));
    }
    
    public function newsletterAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('core/template', 'customer.newsletter')        	
            ->setTemplate('customer/form/newsletter.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function newsletterPostAction()
    {
        
    }
    
    public function mytagsAction() {
    	$this->loadLayout();
        
    	$collection = Mage::getModel('tag/tag')->getCollection();
        $collection->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addStatusFilter(1)
            ->addEntityFilter('customer', Mage::getSingleton('customer/session')->getCustomerId())
            ->load();
            
        $block = $this->getLayout()->createBlock('core/template', 'customer.newsletter')
        	->assign('collection', $collection->getItems())
            ->setTemplate('tag/mytags.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function balanceAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('core/template', 'customer.balance')
            ->setTemplate('customer/balance.phtml')
            ->assign('messages',    Mage::getSingleton('customer/session')->getMessages(true))
            ->assign('customer', Mage::getSingleton('customer/session')->getCustomer());
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
}// Class Mage_Customer_AccountController END
