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
     * When there's an API error
     */
    public function errorAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * When user camcels transaction on paypal site and return back in store
     *
     */
    public function cancelAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * PayPal session instance getter
     * @return Mage_PayPal_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Return checkout session object
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
     * IPN Notify url. process IPN acion.
     */
    public function notifyAction()
    {
        $ipn = Mage::getModel('paypal/api_ipn');
        $ipn->setIpnFormData($this->getRequest()->getParams());
        $ipn->processIpnRequest();
    }

    /**
     * When a customer clicks Paypal button on shopping cart
     */
    public function shortcutAction()
    {
        $this->_getExpress()->shortcutSetExpressCheckout();
        $this->getResponse()->setRedirect($this->_getExpress()->getRedirectUrl());
    }

    /**
     * redirect customer back to paypal, to edit his payment account data
     *
     */
    public function editAction()
    {
        $this->getResponse()->setRedirect($this->_getExpressApi()->getPaypalUrl());
    }

    /**
     * Return here from Paypal before final payment (continue)
     *
     */
    public function returnAction()
    {
        $this->_getExpress()->returnFromPaypal();
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
     *
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
     * @deprecated after 1.4.0.0-alpha3
     */
    public function getReview()
    {
        return $this->_getReview();
    }

    /**
     * Get PayPal Onepage checkout model
     *
     * @return Mage_Paypal_Model_Express_Onepage
     */
    protected function _getReview()
    {
        return Mage::getSingleton('paypal/express_review');
    }

    /**
     * Action for ajax request to save selected shipping and return html block
     *
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
        $result = $this->_getReview()->saveShippingMethod($data);

        if ($this->getRequest()->getParam('ajax')) {
            $this->loadLayout('paypal_express_review_details');
            $this->getResponse()->setBody($this->getLayout()->getBlock('root')->toHtml());
        } else {
            $this->_redirect('paypal/express/review');
        }
    }

    /**
     * Action executed when 'Place Order' button pressed on review page
     *
     */
    public function saveOrderAction()
    {
        /**
         * 1- create order
         * 2- place order (call doexpress checkout)
         * 3- save order
         */
        $error_message = '';
        $payPalSession = $this->_getSession();

        try {
            $address = $this->_getReview()->getQuote()->getShippingAddress();
            if (!$address->getShippingMethod()) {
                if ($shippingMethod = $this->getRequest()->getParam('shipping_method')) {
                    $this->_getReview()->saveShippingMethod($shippingMethod);
                } else if (!$this->_getReview()->getQuote()->getIsVirtual()) {
                    $payPalSession->addError(Mage::helper('paypal')->__('Please select a valid shipping method'));
                    $this->_redirect('paypal/express/review');
                    return;
                }
            }

            $customer = $this->_getReview()->getQuote()->getCustomer();
            if (!$customer || !$customer->getId()) {
                $this->_getReview()->getQuote()
                    ->setCustomerIsGuest(true)
                    ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            }
            unset($customer); // for backward compatibility, see logic after place order

            $billing = $this->_getReview()->getQuote()->getBillingAddress();
            $shipping = $this->_getReview()->getQuote()->getShippingAddress();

            /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
            $convertQuote = Mage::getModel('sales/convert_quote');

            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order');

            if ($this->_getReview()->getQuote()->isVirtual()) {
                $order = $convertQuote->addressToOrder($billing);
            } else {
                $order = $convertQuote->addressToOrder($shipping);
            }

            $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
            $order->setPayment($convertQuote->paymentToOrderPayment($this->_getReview()->getQuote()->getPayment()));

            foreach ($this->_getReview()->getQuote()->getAllItems() as $item) {
                $order->addItem($convertQuote->itemToOrderItem($item));
            }

            /**
             * We can use configuration data for declare new order status
             */
            Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$this->_getReview()->getQuote()));

            //customer checkout from shopping cart page
            if (!$order->getCustomerEmail()) {
                $order->setCustomerEmail($shipping->getEmail());
            }

            $order->place();

            if (isset($customer) && $customer && $this->_getReview()->getQuote()->getCheckoutMethod()==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
                $customer->save();
                $customer->setDefaultBilling($customerBilling->getId());
                $customerShippingId = isset($customerShipping) ? $customerShipping->getId() : $customerBilling->getId();
                $customer->setDefaultShipping($customerShippingId);
                $customer->save();

                $order->setCustomerId($customer->getId())
                    ->setCustomerEmail($customer->getEmail())
                    ->setCustomerPrefix($customer->getPrefix())
                    ->setCustomerFirstname($customer->getFirstname())
                    ->setCustomerMiddlename($customer->getMiddlename())
                    ->setCustomerLastname($customer->getLastname())
                    ->setCustomerSuffix($customer->getSuffix())
                    ->setCustomerGroupId($customer->getGroupId())
                    ->setCustomerTaxClassId($customer->getTaxClassId());

                $billing->setCustomerId($customer->getId())->setCustomerAddressId($customerBilling->getId());
                $shipping->setCustomerId($customer->getId())->setCustomerAddressId($customerShippingId);
            }

        } catch (Mage_Core_Exception $e){
            $error_message = $e->getMessage();
        } catch (Exception $e){
            if (isset($order)) {
                $error_message = $order->getErrors();
            } else {
                $error_message = $e->getMessage();
            }
        }

        if ($error_message) {
            $payPalSession->addError($e->getMessage());
            $this->_redirect('paypal/express/review');
            return;
        }

        if ($order->hasInvoices() && $this->_getExpress()->canSendEmailCopy()) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoice->sendEmail()->setEmailSent(true);
            }
        }
        $order->save();
        $order->sendNewOrderEmail();

        $this->_getReview()->getQuote()->setIsActive(false);
        $this->_getReview()->getQuote()->save();

        $orderId = $order->getIncrementId();
        $this->_getReview()->getCheckout()->setLastQuoteId($this->_getReview()->getQuote()->getId());
        $this->_getReview()->getCheckout()->setLastSuccessQuoteId($this->_getReview()->getQuote()->getId());
        $this->_getReview()->getCheckout()->setLastOrderId($order->getId());
        $this->_getReview()->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $redirect = $this->_getExpress()->getGiropayRedirectUrl();
        $payPalSession->unsExpressCheckoutMethod();

        if ($redirect) {
            $this->getResponse()->setRedirect($redirect);
        } else {
            $this->_redirect('checkout/onepage/success');
        }
    }


    /**
     * Method to update order if customer used PayPal Express
     * as payment method not a separate checkout from shopping cart
     *
     */
    public function updateOrderAction()
    {
        $error_message = '';
        $payPalSession = $this->_getSession();

        $order = Mage::getModel('sales/order')->load($this->_getCheckout()->getLastOrderId());

        if ($order->getId()) {
            $comment = null;
            $transaction = Mage::getModel('core/resource_transaction')
               ->addObject($order);
            if ($order->canInvoice() && $this->_getExpress()->getPaymentAction() == Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_SALE) {

                $invoice = $order->prepareInvoice();

                try{
                    $invoice->register()->capture();
                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                    $this->_redirect('paypal/express/review');
                    return;
                } catch (Exception $e) {
                    Mage::helper('checkout')->sendPaymentFailedEmail($this->_getQuote(), $e->getMessage());
                    $this->_getSession()->addError(Mage::helper('paypal')->__('Sorry, Technical problem!'));
                    $this->_redirect('paypal/express/review');
                    return;
                }

                $transaction->addObject($invoice);
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $orderStatus = $this->_getExpress()->getConfigData('order_status');
                $comment = Mage::helper('paypal')->__('Invoice was created');

            } else {
                try{
                    $this->_getExpress()->placeOrder($order->getPayment());
                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                    $this->_redirect('paypal/express/review');
                    return;
                } catch (Exception $e) {
                    Mage::helper('checkout')->sendPaymentFailedEmail($this->_getQuote(), $e->getMessage());
                    $this->_getSession()->addError(Mage::helper('paypal')->__('Sorry, Technical problem!'));
                    $this->_redirect('paypal/express/review');
                    return;
                }

                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $orderStatus = $this->_getExpress()->getConfigData('order_status');
            }

            if (!$orderStatus) {
                $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
            }
            if (!$comment) {
                $comment = Mage::helper('paypal')->__('Customer returned from PayPal site.');
            }

            $order->setState($orderState, $orderStatus, $comment, $notified = true);
            $transaction->save();

            $this->_getQuote()->setIsActive(false);
            $this->_getQuote()->save();

            $order->sendNewOrderEmail();
        }

        $payPalSession->unsExpressCheckoutMethod();
        if ($redirect = $this->_getExpress()->getGiropayRedirectUrl()) {
            $this->getResponse()->setRedirect($redirect);
        } else {
            $this->_redirect('checkout/onepage/success');
        }
    }
}
