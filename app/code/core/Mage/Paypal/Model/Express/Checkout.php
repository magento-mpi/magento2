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
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 */
class Mage_Paypal_Model_Express_Checkout
{
    /**
     * @var Mage_Paypal_Model_Express
     */
    protected $_method = null;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * State helper variables
     * @var string
     */
    protected $_redirectUrl = '';
    protected $_pendingPaymentMessage = '';
    protected $_checkoutRedirectUrl = '';

    /**
     * Set quote and payment method instance
     * @param array $params
     */
    public function __construct($params = array())
    {
        if (isset($params['method_instance']) && $params['method_instance'] instanceof Mage_Paypal_Model_Express) {
            $this->_method = $params['method_instance'];
        } else {
            Mage::throwException(Mage::helper('paypal')->__('PayPal Express Checkout requires payment method instance.'));
        }

        if (isset($params['quote']) && $params['quote'] instanceof Mage_Sales_Model_Quote) {
            $this->_quote = $params['quote'];
        } else {
            Mage::throwException(Mage::helper('paypal')->__('PayPal Express Checkout requires quote instance.'));
        }
    }

    /**
     * Reserve order ID for specified quote and start checkout on PayPal
     * @return string
     */
    public function start($returnUrl, $cancelUrl)
    {
        $api = $this->_method->getApi();
        $this->_quote->reserveOrderId()->save();
        if ($this->_quote->getIsVirtual()) {
            $shippingAddress = false;
        } else {
            $shippingAddress = $this->_quote->getShippingAddress();
            if (!$shippingAddress->getId()) {
                $shippingAddress = null;
            }
        }
        $token = $this->_method->startExpressCheckout($this->_quote->getBaseGrandTotal(),
            $this->_quote->getBaseCurrencyCode(), $this->_quote->getReservedOrderId(),
            $returnUrl, $cancelUrl, $shippingAddress
        );
        // TODO: collect additional information
        $this->_redirectUrl = $this->_method->getExpressCheckoutStartUrl();
        return $token;
    }

    /**
     * Update quote when returned from PayPal
     * @param string $token
     */
    public function returnFromPaypal($token = null)
    {
        $api = $this->_method->getExpressCheckoutDetails($token = null);

        // import billing address data from PayPal
        $billingAddress = $this->_quote->getBillingAddress();
        foreach ($api->getExportedBillingAddress()->getData() as $key => $value) {
            $billingAddress->setDataUsingMethod($key, $value);
        }

        // as well import shipping address data from PayPal
        if ((!$this->_quote->getIsVirtual()) && $shippingAddress = $this->_quote->getShippingAddress()) {
            foreach ($api->getExportedShippingAddress()->getData() as $key => $value) {
                $shippingAddress->setDataUsingMethod($key, $value);
            }
            $shippingAddress->setCollectShippingRates(true);
        }

        // update quote payment info
        $payment = $this->_quote->getPayment();
        $payment->setMethod($this->_method->getCode());
        Mage::getSingleton('paypal/info')->importToPayment($api, $payment);
//        if ($this->_method->canStoreFraud()) {
//            $this->_quote->getPayment()->setFraudFlag(true);
//        }

        $this->_quote->collectTotals()->save();
    }

    /**
     * Check whether order review has enough data to initialize
     * @param $token
     * @throws Mage_Core_Exception
     */
    public function prepareOrderReview($token = null)
    {
        $payment = $this->_quote->getPayment();
        if (!$payment || !$payment->getAdditionalInformation('paypal_payer_id')) {
            Mage::throwException(Mage::helper('paypal')->__('Payer is not identified.'));
        }
    }

    /**
     * Set shipping method to quote, if needed
     * @param string $methodCode
     */
    public function updateShippingMethod($methodCode)
    {
        if (!$this->_quote->getIsVirtual() && $shippingAddress = $this->_quote->getShippingAddress()) {
            if (!$shippingAddress->getId() || $methodCode != $shippingAddress->getShippingMethod()) {
                $shippingAddress->setShippingMethod($methodCode)->setCollectShippingRates(true);
                $this->_quote->collectTotals()->save();
            }
        }
    }

    /**
     * Place the order when customer returned from paypal
     * Until this moment all quote data must be valid
     *
     * @return Mage_Sales_Model_Order
     */
    public function placeOrder($token = null, $shippingMethodCode = null)
    {
        if ($shippingMethodCode) {
            $this->updateShippingMethod($shippingMethodCode);
        }
        $order = Mage::getModel('sales/service_quote', $this->_quote)->submit();
        $this->_quote->save();

        switch ($order->getState()) {
            // even after placement paypal can disallow to authorize/capture, but will wait until bank transfers money
            case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:
                if ($this->_method->getApi()->getIsRedirectRequired()) {
                    $this->_redirectUrl = $this->_method->getApi()->getExpressCompleteUrl();
                }
                // explain reason why order is in pending payment
                $ths->_pendingPaymentMessage = 'xz';
                break;
            // regular placement, when everything is ok
            case Mage_Sales_Model_Order::STATE_PROCESSING:
            case Mage_Sales_Model_Order::STATE_COMPLETE:
                if ($order->getPayment()->getCreatedInvoice() && $this->_method->canSendEmailCopy()) {
                   $order->sendNewOrderEmail();
                }
                break;
        }
        return $order;
    }

//    /**
//     * Perform API call to start transaction from shopping cart
//     *
//     * @return Mage_Paypal_Model_Express_Checkout
//     */
//    public function shortcutSetExpressCheckout()
//    {
//        $api = $this->_method->getApi();
//        $this->_quote->reserveOrderId()->save();
//        $api->setSolutionType($this->_method->getSolutionType())
//            ->setPayment($this->_method->getPayment())
//            ->setPaymentType($this->_method->getPaymentAction())
//            ->setAmount($this->_quote->getBaseGrandTotal())
//            ->setCurrencyCode($this->_quote->getBaseCurrencyCode())
//            ->setInvNum($this->_quote->getReservedOrderId());
//
//        $api->callSetExpressCheckout();
//
//        $this->_method->catchError();
//
//        $this->_method->getSession()->setExpressCheckoutMethod('shortcut');
//
//        return $this;
//    }

    /**
     * Determine whether redirect somewhere specifically is required
     *
     * @param string $action
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

//    public function getPendingPaymentMessage()
//    {
//        return $this->_pendingPaymentMessage;
//    }
}
