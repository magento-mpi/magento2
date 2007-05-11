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
        if (!preg_match('#^(create|login|forgotpassword|forgotpasswordpost)#', $action)) {
            if (!Mage::getSingleton('customer', 'session')->authenticate($this)) {
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
        
        $block = $this->getLayout()->createBlock('customer_account', 'customer.account')
            ->assign('wishlistActive', Mage::getConfig()->getModule('Mage_Customer')->is('wishlistActive'))
            ->assign('messages', Mage::getSingleton('customer', 'session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function loginAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('customer_login', 'customer.login')
            ->assign('messages', Mage::getSingleton('customer', 'session')->getMessages(true))
            ->assign('postAction', Mage::getUrl('customer', array('controller'=>'account', 'action'=>'loginPost', '_secure'=>true)));
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function loginPostAction()
    {
        $session = Mage::getSingleton('customer', 'session');
        
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login)) {
                extract($login);
                if (!empty($username) && !empty($password)) {
                    if ($session->login($username, $password)) {
                        //$this->getResponse()->setRedirect($session->getUrlBeforeAuthentication());
                        //return true;
                    }
                    else {
                        $session->addMessage(Mage::getModel('customer', 'message')->error('CSTE000'));
                    }
                }
            }
        }
        $this->getResponse()->setRedirect($session->getUrlBeforeAuthentication());
        //$this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'login', '_secure'=>true)));
    }
    
    public function logoutAction()
    {
        Mage::getSingleton('customer', 'session')->logout();
        $this->getResponse()->setRedirect(Mage::getBaseUrl());
    }
    
    /**
     * Registration form
     *
     */
    public function createAction()
    {
        $this->loadLayout();
        
        // if customer logged in
        if (Mage::getSingleton('customer', 'session')->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account')));
            return;
        }
        
        $countries = Mage::getModel('directory', 'country_collection');
        $data = Mage::getSingleton('customer', 'session')->getCustomerFormData(true);
        if (!$data) {
            $data = new Varien_Object();
        }

        $block = $this->getLayout()->createBlock('tpl', 'customer.regform')
            ->setTemplate('customer/form/registration.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'createPost', '_secure'=>true)))
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('regions',     $countries->getDefault($data->getCountryId())->getRegions())
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
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
                Mage::getSingleton('customer', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer', 'message')->success('CSTS001'));

                $mailer = Mage::getModel('customer', 'email')
                        ->setTemplate('email/welcome.phtml')
                        ->setType('html')
                        ->setCustomer($customer)
                        ->send();
                        
                $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account')));
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer', 'session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        
        $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'create', '_secure'=>true)));
    }
    
    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.forgotpassword')
            ->setTemplate('customer/form/forgotpassword.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'forgotpasswordpost')))
            ->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function forgotPasswordPostAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            $customer = Mage::getModel('customer', 'customer')->loadByEmail($email);
            if ($customer->getId()) {
                try {
                    $newPassword = Mage::getModel('core', 'cookie')->randomSequence(8);
                    $data = array(
                        'password'      => $newPassword,
                        'confirmation'  => $newPassword
                    );
                    
                    $customer->changePassword($data, false)
                        ->setPassword($newPassword);
                        
                    $mailer = Mage::getModel('customer', 'email')
                        ->setTemplate('email/forgot_password.phtml')
                        ->setCustomer($customer)
                        ->send();
                    
                    Mage::getSingleton('customer', 'session')
                        ->addMessage(Mage::getModel('customer', 'message')->success('CSTS006'));
                    
                    $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account')));
                    return;
                }
                catch (Exception $e){
                    echo $e;
                }
            }
            else {
                Mage::getSingleton('customer', 'session')
                    ->addMessage(Mage::getModel('customer', 'message')->error('CSTE024'));
               $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'forgotpassword')));
            }
        }
    }


    public function editAction()
    {
        $this->loadLayout();
        
        $data = Mage::getSingleton('customer', 'session')->getCustomerFormData(true);
        if (!$data || $data->isEmpty()) {
            $data = Mage::getSingleton('customer', 'session')->getCustomer();
        }
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.edit')
            ->setTemplate('customer/form/edit.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'editPost')))
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());
            $customer->setCustomerId(Mage::getSingleton('customer', 'session')->getCustomerId());
            
            try {
                $customer->save();
                Mage::getSingleton('customer', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer', 'message')->success('CSTS002'));
                
                $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account')));
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer', 'session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'edit')));
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.changepassword')
            ->setTemplate('customer/form/changepassword.phtml')
            ->assign('action',      Mage::getUrl('customer', array('controller'=>'account', 'action'=>'changePasswordPost', '_secure'=>true)))
            ->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function changePasswordPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getSingleton('customer', 'session')->getCustomer();
            
            try {
                $customer->changePassword($this->getRequest()->getPost());
                
                Mage::getSingleton('customer', 'session')
                    ->addMessage(Mage::getModel('customer', 'message')->success('CSTS003'));

                $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account')));
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer', 'session')->addMessages($e->getMessages());
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'changePassword', '_secure'=>true)));
    }
    
    public function newsletterAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.newsletter')
            ->setTemplate('customer/form/newsletter.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function newsletterPostAction()
    {
        
    }
    
    public function balanceAction()
    {
        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.balance')
            ->setTemplate('customer/balance.phtml')
            ->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true))
            ->assign('customer', Mage::getSingleton('customer', 'session')->getCustomer());
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
}// Class Mage_Customer_AccountController END