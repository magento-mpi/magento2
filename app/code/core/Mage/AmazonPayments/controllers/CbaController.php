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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_AmazonPayments_CbaController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get singleton with Checkout by Amazon order transaction information
     *
     * @return Mage_AmazonPayments_Model_Payment_CBA
     */
    public function getCba()
    {
        return Mage::getSingleton('amazonpayments/payment_cba');
    }

    /**
     * When a customer clicks Checkout with Amazon button on shopping cart
     */
    public function shortcutAction()
    {
        if (!$this->getCba()->isAvailable()) {
            $this->_redirect('checkout/cart/');
        }
        $this->getCba()->shortcutSetCbaCheckout();
        $this->getResponse()->setRedirect($this->getCba()->getRedirectUrl());
    }

    /**
     * When a customer chooses Checkout by Amazon on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        if (!$this->getCba()->isAvailable()) {
            $this->_redirect('checkout/cart/');
        }
        $session = $this->getCheckout();
        $_quote = $this->getCheckout()->getQuote();
        $payment = $this->getCheckout()->getQuote()->getPayment();

        /*echo '<pre> quote:'."\n";
        print_r($_quote->getData());
        echo " payment:\n";
        print_r($payment->getData());
        echo '</pre>'."\n";*/

        $this->getResponse()->setBody($this->getLayout()->createBlock('amazonpayments/cba_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer has checkout on Amazon and return with Successful payment
     *
     */
    public function successAction()
    {
        $error_message = '';

        /*Array
        (
            [amznPmtsOrderIds] => 102-7389301-2720225
            [showAmznPmtsTYPopup] => 1
            [merchName] => Varien
            [amznPmtsYALink] => http://kv.no-ip.org/dev/andrey.babich/magento/index.php/amazonpayments/cba/return/?amznPmtsOrderIds=102-7389301-272022&
        )*/

        $this->getCba()->returnAmazon();

        $quote = $this->getCheckout()->getQuote();
        #$payment = $this->getCheckout()->getQuote()->getPayment();
        /*echo '<pre>'."\n";
        echo " quote:\n";
        print_r($quote->getData());
        echo " payment:\n";
        print_r($quote->getPayment()->getData());
        echo '</pre>'."\n";*/

        $billing = $quote->getBillingAddress();
        $shipping = $quote->getShippingAddress();

        $convertQuote = Mage::getModel('sales/convert_quote');
        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
        $order = Mage::getModel('sales/order');
        /* @var $order Mage_Sales_Model_Order */

        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
        $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
        // add payment information to order

        foreach ($quote->getAllItems() as $item) {
            $order->addItem($convertQuote->itemToOrderItem($item));
        }
        // add items to order
        #$order->save();

        $order->place();

        if (isset($customer) && $customer && $quote->getCheckoutMethod()=='register') {
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

        $order->save();

        $quote->setIsActive(false);
        $quote->save();

        $orderId = $order->getIncrementId();
        $this->getCheckout()->setLastQuoteId($quote->getId());
        $this->getCheckout()->setLastSuccessQuoteId($quote->getId());
        $this->getCheckout()->setLastOrderId($order->getId());
        $this->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $order->sendNewOrderEmail();

        #$payPalSession->unsExpressCheckoutMethod();

        $this->_redirect('checkout/onepage/success');

        #$amazonOrderDetails = $this->getCba()->getAmazonOrderDetails();
        #echo "amazonOrderDetails<br />\n";

        #$payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
        #echo ($payment && $payment->getAmazonOrderId());

        #die('success');

        #$this->_redirect('checkout/onepage/success');
    }

    /**
     * When a customer has checkout on Amazon and return with Cancel
     *
     */
    public function cancelAction()
    {
        #die('cancel');
        $this->_redirect('checkout/cart/');
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
        $payPalSession = Mage::getSingleton('paypal/session');

        try {
            $address = $this->getReview()->getQuote()->getShippingAddress();
            if (!$address->getShippingMethod()) {
                if ($shippingMethod = $this->getRequest()->getParam('shipping_method')) {
                    $this->getReview()->saveShippingMethod($shippingMethod);
                } else if (!$this->getReview()->getQuote()->getIsVirtual()) {
                    $payPalSession->addError(Mage::helper('paypal')->__('Please select a valid shipping method'));
                    $this->_redirect('paypal/express/review');
                    return;
                }
            }

            $billing = $this->getReview()->getQuote()->getBillingAddress();
            $shipping = $this->getReview()->getQuote()->getShippingAddress();

            $convertQuote = Mage::getModel('sales/convert_quote');
            /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
            $order = Mage::getModel('sales/order');
            /* @var $order Mage_Sales_Model_Order */

            if ($this->getReview()->getQuote()->isVirtual()) {
                $order = $convertQuote->addressToOrder($billing);
            } else {
                $order = $convertQuote->addressToOrder($shipping);
            }

            $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
            $order->setPayment($convertQuote->paymentToOrderPayment($this->getReview()->getQuote()->getPayment()));

            foreach ($this->getReview()->getQuote()->getAllItems() as $item) {
                $order->addItem($convertQuote->itemToOrderItem($item));
            }

            /**
             * We can use configuration data for declare new order status
             */
            Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$this->getReview()->getQuote()));

            //customer checkout from shopping cart page
            if (!$order->getCustomerEmail()) {
                $order->setCustomerEmail($shipping->getEmail());
            }

            $order->place();

            if (isset($customer) && $customer && $this->getReview()->getQuote()->getCheckoutMethod()=='register') {
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

        $order->save();

        $this->getReview()->getQuote()->setIsActive(false);
        $this->getReview()->getQuote()->save();

        $orderId = $order->getIncrementId();
        $this->getReview()->getCheckout()->setLastQuoteId($this->getReview()->getQuote()->getId());
        $this->getReview()->getCheckout()->setLastSuccessQuoteId($this->getReview()->getQuote()->getId());
        $this->getReview()->getCheckout()->setLastOrderId($order->getId());
        $this->getReview()->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $order->sendNewOrderEmail();

        $payPalSession->unsExpressCheckoutMethod();

        $this->_redirect('checkout/onepage/success');
    }

}