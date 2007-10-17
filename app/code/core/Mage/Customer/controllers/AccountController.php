<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_AccountController extends Mage_Core_Controller_Front_Action
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
        if (!preg_match('#^(create|login|logoutSuccess|forgotpassword|forgotpasswordpost)#', $action)) {
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

        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('customer/account_dashboard'));
        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));

        $this->renderLayout();
    }

    /**
     * Customer login form
     */
    public function loginAction()
    {
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
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
                        $session->addError(__('Invalid login or password'));
                        Mage::getSingleton('customer/session')->setUsername($username);
                    }
                }
            }
        }
        if (!$session->getBeforeAuthUrl()) {
            $session->setBeforeAuthUrl(Mage::getUrl('customer/account'));
        }
        $this->getResponse()->setRedirect($session->getBeforeAuthUrl());
    }

    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        Mage::getSingleton('customer/session')
            ->logout()
            ->setBeforeAuthUrl(Mage::getUrl());

        $this->_redirect('*/*/logoutSuccess');
    }

    public function logoutSuccessAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer register form
     */
    public function createAction()
    {
        // if customer logged in
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->loadLayout();
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
                    ->addSuccess(__('Thank you for registering with %s', Mage::app()->getStore()->getName()) );

                $customer->sendNewAccountEmail();

                $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
                if (Mage::getSingleton('customer/session')->getBeforeAuthUrl()) {
                    $successUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
                }
                $this->_redirectSuccess($successUrl);
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
                        ->addSuccess(__('A new password was sent'));

                    $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                    return;
                }
                catch (Exception $e){
                    Mage::getSingleton('customer/session')
                        ->addError($e->getMessage());
                }
            }
            else {
                Mage::getSingleton('customer/session')
                    ->addError(__('This email address was not found in our records'));
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
    }


    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $data = Mage::getSingleton('customer/session')->getCustomerFormData(true);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!empty($data)) {
            $customer->addData($data);
        }
        $this->renderLayout();
    }

    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')
                ->setData($this->getRequest()->getPost())
                ->setId(Mage::getSingleton('customer/session')->getCustomerId());

            $currPass = $this->getRequest()->getPost('current_password');
            $newPass  = $this->getRequest()->getPost('password');
            if ($currPass && $newPass) {
                $currentCustomerPass = Mage::getSingleton('customer/session')->getCustomer()->getPasswordHash();
                if ($currentCustomerPass == $customer->hashPassword($currPass)) {
                    $customer->setPassword($newPass);
                }
                else {
                    Mage::getSingleton('customer/session')
                        ->setCustomerFormData($this->getRequest()->getPost())
                        ->addError(__('Invalid current password'));
                    $this->_redirect('*/*/edit');
                    return;
                }
            }

            try {
                $customer->save();
                Mage::getSingleton('customer/session')
                    ->setCustomer($customer)
                    ->addSuccess(__('Account information was successfully saved'));

                $this->_redirect('customer/account');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit');
    }
}// Class Mage_Customer_AccountController END
