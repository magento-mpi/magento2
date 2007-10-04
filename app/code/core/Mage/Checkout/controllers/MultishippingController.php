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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout controller
 *
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_MultishippingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve checkout model
     *
     * @return Mage_Checkout_Model_Multishipping
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }
    
    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Retrieve checkout state model
     *
     * @return Mage_Checkot_Model_Type_Multishipping_State
     */
    protected function _getState()
    {
        return Mage::getSingleton('checkout/type_multishipping_state');
    }
    
    /**
     * Retrieve checkout url heler
     *
     * @return Mage_Checkout_Helper_Url
     */
    protected function _getUrlHelper()
    {
        return Mage::helper('checkout/url');
    }
    
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!$this->_getCheckoutSession()->getQuote()->hasItems() 
            || $this->_getCheckoutSession()->getQuote()->hasItemsWithDecimalQty()) 
        {
            $this->_redirectUrl($this->_getUrlHelper()->getCartUrl());
            $this->setFlag('', 'no-dispatch', true);
            return;
        }
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^(login|register)#', $action)) {
            if (!Mage::getSingleton('customer/session')->authenticate($this, $this->_getUrlHelper()->getMSLoginUrl())) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
        return $this;
    }

    /**
     * Index action of Multishipping checkout
     */
    public function indexAction()
    {
        $this->_redirectUrl($this->_getUrlHelper()->getMSAddressesUrl());
    }

    /**
     * Multishipping checkout login page
     */
    public function loginAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl($this->_getUrlHelper()->getMSAddressesUrl());
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        // set account create url
        if ($loginForm = $this->getLayout()->getBlock('customer_form_login')) {
            $loginForm->setCreateAccountUrl($this->_getUrlHelper()->getMSRegisterUrl());
        }
        $this->renderLayout();
    }

    /**
     * Multishipping checkout login page
     */
    public function registerAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl($this->_getUrlHelper()->getMSCheckoutUrl());
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        if ($registerForm = $this->getLayout()->getBlock('customer_form_register')) {
            $registerForm->setShowAddressFields(true)
                ->setBackUrl($this->_getUrlHelper()->getMSLoginUrl())
                ->setSuccessUrl($this->_getUrlHelper()->getMSAddressesUrl())
                ->setErrorUrl($this->_getUrlHelper()->getCurrentUrl());
        }

        $this->renderLayout();
    }

    /**
     * Multishipping checkout select address page
     */
    public function addressesAction()
    {
        // If customer do not have addresses
        if (!$this->_getCheckout()->getCustomerDefaultShippingAddress()) {
            $this->_redirect('*/multishipping_address/newShipping');
            return;
        }
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES);
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    /**
     * Multishipping checkout process posted addresses
     */
    public function addressesPostAction()
    {
        if ($shipToInfo = $this->getRequest()->getPost('ship')) {
            $this->_getCheckout()->setShippingItemsInformation($shipToInfo);
        }
        if ($this->getRequest()->getParam('continue', false)) {
            $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING);
            $this->_redirect('*/*/shipping');
        }
        else {
            $this->_redirect('*/*/addresses');
        }
    }

    public function backToAddressesAction()
    {
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES);
        $this->_redirect('*/*/addresses');
    }

    /**
     * Multishipping checkout remove item action
     */
    public function removeItemAction()
    {
        $itemId     = $this->getRequest()->getParam('id');
        $addressId  = $this->getRequest()->getParam('address');
        if ($addressId && $itemId) {
            $this->_getCheckout()->removeAddressItem($addressId, $itemId);
        }
        $this->_redirect('*/*/addresses');
    }

    /**
     * Multishipping checkout shipping information page
     */
    public function shippingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function backToShippingAction()
    {
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING);
        $this->_redirect('*/*/shipping');
    }

    public function shippingPostAction()
    {
        $shippingMethods = $this->getRequest()->getPost('shipping_method');
        try {
            $this->_getCheckout()->setShippingMethods($shippingMethods);
            $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING);
            $this->_redirect('*/*/billing');
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            $this->_redirect('*/*/shipping');
        }
    }

    /**
     * Multishipping checkout billing information page
     */
    public function billingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function billingPostAction()
    {
        $payment = $this->getRequest()->getPost('payment');
        try {
            $this->_getCheckout()->setPaymentMethod($payment);
            $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_OVERVIEW);
            $this->_redirect('*/*/overview');
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            $this->_redirect('*/*/billing');
        }
    }

    public function backToBillingAction()
    {
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING);
        $this->_redirect('*/*/billing');
    }

    /**
     * Multishipping checkout place order page
     */
    public function overviewAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function overviewPostAction()
    {
        try {
            $this->_getCheckout()->createOrders();
            $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SUCCESS);
            $this->_redirect('*/*/success');
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            $this->_redirect('*/*/overview');
        }
    }

    /**
     * Multishipping checkout succes page
     */
    public function successAction()
    {
        $this->_getCheckout()->getCheckoutSession()->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
}
