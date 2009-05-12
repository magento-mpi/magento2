<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Invitation customer account frontend controller
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize invitation from request
     *
     * @return Enterprise_Invitation_Model_Invitation
     */
    protected function _initInvitation()
    {
        if (!Mage::registry('customer_invitation')) {
            $invitation = Mage::getModel('enterprise_invitation/invitation');
            $invitationCode = $this->getRequest()->getParam('invitation', false);
            $invitation->loadByInvitationCode($invitationCode);
            Mage::register('customer_invitation', $invitation);
            return $invitation;
        }

        return Mage::registry('customer_invitation');
    }

    /**
     * Customer register form page
     */
    public function createAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }

        if ((!Mage::helper('enterprise_invitation')->isEnabled()) ||
            (!Mage::helper('enterprise_invitation')->isRegistrationAllowed()))
          {
            $this->_redirect('customer/account/create');
            return;
        }

        $invitation = $this->_initInvitation();
        if (!$invitation->getId() && Mage::helper('enterprise_invitation')->getInvitationRequired()) {
            $this->_getSession()->addError(Mage::helper('enterprise_invitation')->__('Registration only by invitation'));
            $this->_redirect('customer/account/login');
            return;
        } elseif (!$invitation->getId()) {
            $this->_getSession()->addError(Mage::helper('enterprise_invitation')->__('This invitation is not valid'));
            $this->_redirect('customer/account/login');
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
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }

        if ((!Mage::helper('enterprise_invitation')->isEnabled())  ||
            (!Mage::helper('enterprise_invitation')->isRegistrationAllowed()))
          {
            $this->_redirect('customer/account/create');
            return;
        }

        if ($this->getRequest()->isPost()) {
            $invitation = $this->_initInvitation();
            $errors = array();

            $customer = Mage::getModel('customer/customer')->setId(null);

            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('create') && ($value = $this->getRequest()->getParam($code)) !== null) {
                    $customer->setData($code, $value);
                }
            }

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                    ->setData($this->getRequest()->getPost())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                    ->setId(null);
                $customer->addAddress($address);

                $errors = $address->validate();
                if (!is_array($errors)) {
                    $errors = array();
                }
            }
            if (!$invitation->getId() &&
                Mage::helper('enterprise_invitation')->getInvitationRequired()) {
                $errors[] = Mage::helper('enterprise_invitation')->__('Invalid invitation link');
            } elseif ($invitation->getId() && $invitation->getGroupId()) {
                $customer->setGroupId($invitation->getGroupId());
            }

            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;



                if (true === $validationResult) {
                    $customer->save();

                    $now = Mage::app()->getLocale()->date()
                        ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
                        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                    $invitation->setReferralId($customer->getId())
                        ->setSignupDate($now)
                        ->setStatus(Enterprise_Invitation_Model_Invitation::STATUS_ACCEPTED)
                        ->save();

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $this->_getSession()->getBeforeAuthUrl());
                        $this->_getSession()->addSuccess(Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.',
                            Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())
                        ));
                        $this->_redirectSuccess(Mage::getUrl('customer/account/index', array('_secure'=>true)));
                        return;
                    }
                    else {
                        $this->_getSession()->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    }
                    else {
                        $this->_getSession()->addError(Mage::helper('customer')->__('Invalid customer data'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setCustomerFormData($this->getRequest()->getPost());
            }
            catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, Mage::helper('customer')->__('Can\'t save customer'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/create', array('_current'=>true,'_secure'=>true)));
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess($this->__('Thank you for registering with %s', Mage::app()->getStore()->getName()));

        $customer->sendNewAccountEmail($isJustConfirmed ? 'confirmed' : 'registered');

        $successUrl = Mage::getUrl('customer/account/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

}
