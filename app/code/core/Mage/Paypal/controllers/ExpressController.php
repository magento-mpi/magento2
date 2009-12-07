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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Express Checkout Controller
 */
class Mage_Paypal_ExpressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Setting right header of response if session died
     *
     */
    protected function _expireAjax()
    {
        if (!$this->_getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     */
    public function getExpress()
    {
        return $this->_getExpress();
    }

    /**
     * Get singleton with paypal express order transaction information
     *
     * @return Mage_Paypal_Model_Express
     */
    protected function _getExpress()
    {
        return Mage::getSingleton('paypal/express');
    }

    /**
     * Return Express payPal method Api object
     *
     * @return Mage_PayPal_Model_Api_Nvp
     */
    protected function _getExpressApi()
    {
        return $this->_getExpress()->getApi();
    }

    /**
     * Return Express payPal method Api object
     *
     * @return Mage_Paypal_Model_Express_Checkout
     */
    protected function _getExpressCheckout()
    {
        return Mage::getSingleton('paypal/express_checkout');
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     */
    public function getReview()
    {
        return $this->_getReview();
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     */
    protected function _getReview()
    {
        return Mage::getSingleton('paypal/express_review');
    }

    /**
     * PayPal session instance getter
     *
     * @return Mage_PayPal_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * When there's an API error
     */
    public function errorAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * When user cancels transaction on paypal site and return back in store
     */
    public function cancelAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * @see Mage_Paypal_IpnController
     */
    public function notifyAction()
    {
        $this->_forward('express', 'ipn');
    }

    /**
     * When a customer clicks Paypal button on shopping cart
     */
    public function shortcutAction()
    {
        $this->_getExpressCheckout()->shortcutSetExpressCheckout($this->_getExpress());
        $this->getResponse()->setRedirect($this->_getExpress()->getRedirectUrl());
    }

    /**
     * Redirect customer back to paypal, to edit his payment account data
     */
    public function editAction()
    {
        $this->getResponse()->setRedirect($this->_getExpressApi()->getPaypalUrl());
    }

    /**
     * Return here from Paypal before final payment (continue)
     */
    public function returnAction()
    {
        $this->_getExpressCheckout()->returnFromPaypal($this->_getExpress());
        $this->getResponse()->setRedirect($this->_getExpress()->getRedirectUrl());
    }

    /**
     * This is shown behind a link on the PayPal site with the bank number.
     * The customer can click thr link to return to the shop.
     * So this url should show the customer that his order is completed
     * and will be shipped as soon as he transfered the money.
     */
    public function bankAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Return here from Paypal after final payment (commit) or after on-site order review
     */
    public function reviewAction()
    {
        $payment = $this->_getQuote()->getPayment();
        if ($payment && $payment->getPaypalPayerId()) {
            $this->loadLayout();
            $this->_initLayoutMessages('paypal/session');
            $this->renderLayout();
        } else {
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Action for ajax request to save selected shipping and return html block
     */
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_expireAjax();
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $data = $this->getRequest()->getParam('shipping_method', '');
        $this->_getExpressCheckout()->saveShippingMethod($data, $this->_getExpress());

        if ($this->getRequest()->getParam('ajax')) {
            $this->loadLayout('paypal_express_review_details');
            $this->getResponse()->setBody($this->getLayout()->getBlock('root')->toHtml());
        } else {
            $this->_redirect('paypal/express/review');
        }
    }

    /**
     * Action executed when 'Place Order' button pressed on review page
     */
    public function saveOrderAction()
    {
        try {
            $address = $this->_getQuote()->getShippingAddress();
            if (!$address->getShippingMethod()) {
                if ($shippingMethod = $this->getRequest()->getParam('shipping_method')) {
                    $this->_getExpressCheckout()->saveShippingMethod($shippingMethod, $this->_getExpress());
                } else if (!$this->_getQuote()->getIsVirtual()) {
                    Mage::throwException(Mage::helper('paypal')->__('Please select a valid shipping method'));
                }
            }

            $this->_getExpressCheckout()->saveOrder($this->_getExpress());

        } catch (Mage_Core_Exception $e){
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('paypal/express/review');
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('There was an error processing your order. Please contact us.'));
            $this->_redirect('paypal/express/review');
            return;
        }

        $this->_getSession()->unsExpressCheckoutMethod();

        if ($redirect = $this->_getExpress()->getGiropayRedirectUrl()) {
            $this->getResponse()->setRedirect($redirect);
        } else {
            $this->_redirect('checkout/onepage/success');
        }
    }

    /**
     * Method to update order if customer used PayPal Express
     * as payment method not a separate checkout from shopping cart
     */
    public function updateOrderAction()
    {
        if ($orderId = $this->_getCheckout()->getLastOrderId()) {
            try{
                $this->_getExpressCheckout()->updateOrder($orderId, $this->_getExpress());
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('paypal/express/review');
                return;
            } catch (Exception $e) {
                Mage::helper('checkout')->sendPaymentFailedEmail($this->_getQuote(), $e->getMessage());
                $this->_getSession()->addError(
                    Mage::helper('paypal')->__('There was an error processing your order. Please contact us.')
                );
                $this->_redirect('paypal/express/review');
                return;
            }
            $this->_getQuote()->setIsActive(false);
            $this->_getQuote()->save();
        }

        $this->_getSession()->unsExpressCheckoutMethod();

        //process Giropay or ordinary payment
        if ($redirect = $this->_getExpress()->getGiropayRedirectUrl()) {
            $this->getResponse()->setRedirect($redirect);
        } else {
            $this->_redirect('checkout/onepage/success');
        }
    }
}
