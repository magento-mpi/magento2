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
        $this->_initLayoutMessages('customer/session');
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/dashboard')
        );
        
        $this->renderLayout();
    }
    
    /**
     * Customer login form
     */
    public function loginAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        
        $block = $this->getLayout()->createBlock('customer/login')
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
        // if customer logged in
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
            return;
        }
        
        $this->loadLayout();        
        $this->_initLayoutMessages('customer/messages');

        $data = Mage::getSingleton('customer/session')->getCustomerFormData(true);
        $data = new Varien_Object($data);
        
        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/registration.phtml')
            ->assign('action',      Mage::getUrl('*/*/createPost', array('_secure'=>true)))
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
            
            $customer = Mage::getModel('customer/customer')
                ->setData($this->getRequest()->getPost());

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
        $this->_initLayoutMessages('customer/session');
        
        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/forgotpassword.phtml')
            ->assign('action',      Mage::getUrl('*/*/forgotpasswordpost'));
            
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
                        ->addError('New password was sended');
                    
                    $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                    return;
                }
                catch (Exception $e){
                    echo $e;
                }
            }
            else {
                Mage::getSingleton('customer/session')
                    ->addWarning('email address was not found in our records');
               $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
    }


    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        
        $data = Mage::getSingleton('customer/session')->getCustomerFormData(true);
        if (!$data || $data->isEmpty()) {
            $data = Mage::getSingleton('customer/session')->getCustomer();
        }
        
        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/edit.phtml')
            ->assign('action',      Mage::getUrl('customer/account/editPost'))
            ->assign('data',        $data);
           
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')->setData($this->getRequest()->getPost());
            $customer->setId(Mage::getSingleton('customer/session')->getCustomerId());
            
            try {
                $customer->save();
                Mage::getSingleton('customer/session')
                    ->setCustomer($customer)
                    ->addSuccess('customer information is saved');
                
                $this->_redirect('customer/account');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/edit'));
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        
        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/changepassword.phtml')
            ->assign('action', Mage::getUrl('*/*/changePasswordPost', array('_secure'=>true)));
            
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
                    ->addSuccess('password has been successfully updated');

                $this->_redirect('customer/account');
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError('an error updating the password');
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/changePassword', array('_secure'=>true)));
    }
    
    public function newsletterAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('core/template')        	
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
