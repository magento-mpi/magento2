<?php
/**
 * Multishipping checkout
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_MultishippingController extends Mage_Core_Controller_Front_Action
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
        if (!preg_match('#^(login|register)#', $action)) {
            $loginUrl = Mage::getUrl('*/*/login', array('_secure'=>true, '_current'=>true));
            if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }

        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->_redirect('*/cart/');
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Index action of Multishipping checkout
     */
    public function indexAction()
    {
        $this->_redirect('*/*/addresses');
    }

    /**
     * Multishipping checkout login page
     */
    public function loginAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/addresses');
            return;
        }

        $this->loadLayout(array('default', 'customer_login'), 'customer_login');
        $this->_initLayoutMessages('customer/session');

        // set account create url
        if ($loginForm = $this->getLayout()->getBlock('customer_form_login')) {
            $loginForm->setCreateAccountUrl(Mage::getUrl('*/*/register'));
        }
        $this->renderLayout();
    }

    /**
     * Multishipping checkout login page
     */
    public function registerAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
            return;
        }

        $this->loadLayout(array('default', 'customer_register'), 'customer_register');
        $this->_initLayoutMessages('customer/session');

        if ($registerForm = $this->getLayout()->getBlock('customer_form_register')) {
            $registerForm->setShowAddressFields(true)
                ->setBackUrl(Mage::getUrl('*/*/login'))
                ->setSuccessUrl(Mage::getUrl('*/*/addresses'))
                ->setErrorUrl(Mage::getUrl('*/*/*'));
        }

        $this->renderLayout();
    }

    /**
     * Multishipping checkout select address page
     */
    public function addressesAction()
    {
        // If customer do not have addresses
        if (!Mage::getSingleton('checkout/type_multishipping')->getCustomerDefaultShippingAddress()) {
            $this->_redirect('*/multishipping_address/newShipping');
            return;
        }
        $this->loadLayout(array('default', 'multishipping', 'multishipping_addresses'), 'multishipping_addresses');
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
            Mage::getSingleton('checkout/type_multishipping')->setShippingItemsInformation($shipToInfo);
        }
        if ($this->getRequest()->getParam('continue')) {
            Mage::getSingleton('checkout/type_multishipping_state')
                ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING);
            $this->_redirect('*/*/shipping');
        }
        else {
            $this->_redirect('*/*/addresses');
        }
    }

    public function backToAddressesAction()
    {
        Mage::getSingleton('checkout/type_multishipping_state')
            ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES);
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
            Mage::getSingleton('checkout/type_multishipping')
                ->removeAddressItem($addressId, $itemId);
        }
        $this->_redirect('*/*/addresses');
    }

    /**
     * Multishipping checkout shipping information page
     */
    public function shippingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'multishipping_shipping'), 'multishipping_shipping');
        //$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function backToShippingAction()
    {
        Mage::getSingleton('checkout/type_multishipping_state')
            ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING);
        $this->_redirect('*/*/shipping');
    }

    public function shippingPostAction()
    {
        $shippingMethods = $this->getRequest()->getPost('shipping_method');
        try {
            Mage::getSingleton('checkout/type_multishipping')
                ->setShippingMethods($shippingMethods);
            Mage::getSingleton('checkout/type_multishipping_state')
                ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING);
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
        $this->loadLayout(array('default', 'multishipping', 'multishipping_billing'), 'multishipping_billing');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function billingPostAction()
    {
        $payment = $this->getRequest()->getPost('payment');
        try {
            Mage::getSingleton('checkout/type_multishipping')
                ->setPaymentMethod($payment);
            Mage::getSingleton('checkout/type_multishipping_state')
                ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_OVERVIEW);
            $this->_redirect('*/*/overview');
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            $this->_redirect('*/*/billing');
        }
    }

    public function backToBillingAction()
    {
        Mage::getSingleton('checkout/type_multishipping_state')
            ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING);
        $this->_redirect('*/*/billing');
    }

    /**
     * Multishipping checkout place order page
     */
    public function overviewAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'multishipping_overview'), 'multishipping_overview');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function overviewPostAction()
    {
        try {
            Mage::getSingleton('checkout/type_multishipping')
                ->createOrders();
            Mage::getSingleton('checkout/type_multishipping_state')
                    ->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SUCCESS);
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
        $this->loadLayout(array('default', 'multishipping', 'multishipping_success'), 'multishipping_success');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
        Mage::getSingleton('checkout/type_multishipping')->getCheckoutSession()->clear();
    }
}
