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


class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action
{

    protected $_data = array();
    protected $_checkout = null;
    protected $_quote = null;

    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()) {
            $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Session Expired')
                ->setHeader('Login-Required', 'true')
                ->sendResponse();
            exit;
        }
    }

    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Checkout page
     */
    public function indexAction()
    {
        #Mage::getSingleton('customer/session')->setTest('onepage');
        if (!$this->getOnepage()->getQuote()->hasItems()) {
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('customer/session')->setBeforeAuthUrl($this->getRequest()->getRequestUri());
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function progressAction()
    {
        $this->_expireAjax();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        $this->_expireAjax();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        $this->_expireAjax();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function successAction()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('checkout/session')->clear();
        $this->loadLayout();
        Mage::dispatchEvent('checkout_onepage_controller_success_action');
        $this->renderLayout();
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        $this->_expireAjax();
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->setBody($address->toJson());
        }
    }

    public function saveMethodAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!empty($data['use_for_shipping'])) {
//                $this->loadLayout('checkout_onepage_shippingMethod');
//                $result['shipping_methods_html'] = $this->getLayout()->getBlock('root')->toHtml();
                $result['shipping_methods_html'] = $this->_getShippingMethodsHtml();
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function saveShippingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

//            $this->loadLayout('checkout_onepage_shippingMethod');
//            $result['shipping_methods_html'] = $this->getLayout()->getBlock('root')->toHtml();
            $result['shipping_methods_html'] = $this->_getShippingMethodsHtml();

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function saveShippingMethodAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }

    }

    public function savePaymentAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            $this->loadLayout('checkout_onepage_review');
            $result['review_html'] = $this->getLayout()->getBlock('root')->toHtml();

            if ($redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl()) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function saveOrderAction()
    {
        $this->_expireAjax();
        $result = $this->getOnepage()->saveOrder();
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}
