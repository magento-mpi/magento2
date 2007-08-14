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
        $this->loadLayout(array('default', 'customer_dashboard'), 'customer_dashboard');

        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('customer/account_dashboard'));

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));

        $this->renderLayout();
    }

    /**
     * Customer login form
     */
    public function loginAction()
    {
        $this->loadLayout(array('default', 'customer_login'), 'customer_login');
        $this->_initLayoutMessages('customer/session');


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
                        Mage::getSingleton('customer/session')->setUsername($username);
                    }
                }
            }
        }
        $this->getResponse()->setRedirect($session->getBeforeAuthUrl());
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

        $this->loadLayout(array('default', 'customer_register'), 'customer_register');
        $this->_initLayoutMessages('customer/session');


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
            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                    ->setData($this->getRequest()->getPost())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                $customer->addAddress($address);
            }

            try {
                $customer->save();
                Mage::getSingleton('customer/session')
                    ->setCustomerAsLoggedIn($customer)
                    // _('customer is registered')
                    ->addSuccess('Customer is registered');

                $customer->sendNewAccountEmail();
                /*
                $mailer = Mage::getModel('customer/email_template')
                    ->setTemplate('email/welcome.phtml')
                    ->setType('html')
                    ->setCustomer($customer)
                    ->send();
				*/
                $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('customer/session')
                    ->addError($e->getMessage())
                    ->setCustomerFormData($this->getRequest()->getPost());
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/create', array('_secure'=>true)));
    }

    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('root')
            ->setTemplate('page/1column.phtml');
        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/forgotpassword.phtml')
            ->assign('action', Mage::getUrl('*/*/forgotpasswordpost'));

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('Password forgotten'));

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
                    $newPassword = $customer->generatePassword();

                    $customer->changePassword($newPassword, false);
                    
                    $customer->sendPasswordReminderEmail();

                    Mage::getSingleton('customer/session')
                        ->addError(__('New password was sent'));

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
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
    }


    public function editAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');
        $this->_initLayoutMessages('customer/session');

        $data = Mage::getSingleton('customer/session')->getCustomerFormData(true);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!empty($data)) {
            $customer->addData($data);
        }

        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/edit.phtml')
            ->assign('action',      Mage::getUrl('customer/account/editPost'))
            ->assign('customer',    $customer);

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('Edit Account Info'));

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
            catch (Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
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
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');
        $this->_initLayoutMessages('customer/session');

        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('customer/form/changepassword.phtml')
            ->assign('action', Mage::getUrl('*/*/changePasswordPost', array('_secure'=>true)));

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('Change Account Password'));

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

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Tags'));

        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    public function balanceAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $block = $this->getLayout()->createBlock('core/template', 'customer.balance')
            ->setTemplate('customer/balance.phtml')
            ->assign('customer', Mage::getSingleton('customer/session')->getCustomer());

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Balance'));

        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }
}// Class Mage_Customer_AccountController END
