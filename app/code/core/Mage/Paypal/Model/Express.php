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
 *
 * PayPal Express Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Express extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'paypal_express';
    protected $_formBlockType = 'paypal/express_form';
    protected $_infoBlockType = 'paypal/express_info';

    /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    //Sometime we need this. Reffer to isInitilizeNeeded() method
    protected $_isInitializeNeeded      = true;

    protected $_allowCurrencyCode = array(
        'AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD',
        'HUF', 'ILS', 'JPY', 'MXN', 'NOK', 'NZD',
        'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD');

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return is_object(Mage::registry('_singleton/checkout/type_onepage'));
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_allowCurrencyCode)) {
            return false;
        }
        return true;
    }

    /**
     * Check whether is visible on cart page
     *
     * @return bool
     */
    public function isVisibleOnCartPage()
    {
        return (bool)$this->getConfigData('visible_on_cart');
    }

    /**
     * Check whether invoice email should be sent
     * @return bool
     */
    public function canSendEmailCopy()
    {
        return (bool)$this->getConfigData('invoice_email_copy');
    }

    /**
     * Return fraud status, if fraud management enabled and api returned fraud suspicious
     * we return true, we may store fraud result, otherwise return false,
     * don't perform any actions with frauds
     *
     * @return bool
     */
    public function canStoreFraud()
    {
        if ($this->getFraudFilterStatus() && $this->getApi()->getIsFraud()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return status if user may perform any action with fraud transaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function canManageFraud(Varien_Object $payment)
    {
        if ($this->getFraudFilterStatus() && $payment->getOrder()->getStatus() == $this->getConfigData('fraud_order_status')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Paypal API Model instance as singleton
     *
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function getApi()
    {
        return Mage::getSingleton('paypal/api_nvp');
    }

    /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

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
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Return fraud filter config valie: enabed/ disabled
     *
     * @return bool
     */
    public function getFraudFilterStatus()
    {
        return $this->getConfigData('fraud_filter');
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getApi()->getRedirectUrl();
    }

    /**
     * Return Api redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getRedirectUrl();
    }

    /**
     * Return giropay redirect url to continue giropay transaction
     *
     * @return string
     */
    public function getGiropayRedirectUrl()
    {
      if ($this->getApi()->getRedirectRequered()) {
          return sprintf($this->getApi()->getGiropayRedirectUrl(), $this->getApi()->getToken());
      }
      return "";
    }

    /**
     * Used for Express Account optional
     *
     * @return string
     */
    public function getSolutionType()
    {
        return $this->getConfigData('solution_type');
    }

    /**
     * Used for enablin line item options
     *
     * @return string
     */
    public function getLineItemEnabled()
    {
        return $this->getConfigData('line_item');
    }

    /**
     * Processing error from paypal
     *
     * @return Mage_Paypal_Model_Express
     */
    public function catchError()
    {
        try {
            $this->throwError();
        } catch (Mage_Core_Exception $e) {
            $this->getCheckout()->addError($e->getMessage());
        }
        return $this;
    }

    /**
     * Works same as catchError method but instead of saving
     * error message in session throws exception
     *
     * @return Mage_Paypal_Model_Express
     */
    public function throwError()
    {
        $e = $this->getApi()->getError();
        if ($e && !empty($e['type'])) {
            switch ($e['type']) {
                case 'CURL':
                    Mage::throwException(Mage::helper('paypal')->__('There was an error connecting to the PayPal server: %s', $e['message']));
                case 'API':
                    Mage::throwException(Mage::helper('paypal')->__('There was an error during communication with PayPal: %s - %s', $e['short_message'], $e['long_message']));
            }
        }
        return $this;
    }

    /**
     * Convert api cal result into exception
     *
     * @param Mage_Paypal_Model_Api_Abstract $api
     * @param mixed $callResult
     * @throws Mage_Core_Exception
     */
    private function _wrapApiError($api, $callResult = null)
    {
        if (false === $callResult) {
            Mage::throwException(Mage::helper('paypal')->__('Unable to communicate with PayPal gateway.'));
        }
        if ($api->getError()) {
            Mage::throwException(
                Mage::helper('paypal')->__('PayPal gateway returned error: %s.', $api->getErrorMessage())
            );
        }
    }

    /**
     * Prepare form block
     *
     * @param string $name Block alias
     * @return Mage_Core_Block_Abstract
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock($this->getFormBlockType(), $name)
            ->setMethod('paypal_express')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/express/form.phtml');

        return $block;
    }

    /**
     * Prepare info block
     *
     * @param string $name Block alias
     * @return Mage_Core_Block_Abstract
     */
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock($this->getInfoBlockType(), $name)
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/express/info.phtml');

        return $block;
    }

    /**
     * Return paypal Express payment action
     *
     * @return string
     */
    public function getPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    /**
     * Get config paypal action url
     * Used to universilize payment actions when processing payment place
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        switch ($paymentAction){
            case Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_SALE:
                $paymentAction = Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
                break;
            case Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_AUTH:
            default:
                $paymentAction = Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
                break;
        }
        return $paymentAction;
    }

    /**
     * Get Pal Detailes for dynamic buttons using
     *
     */
    public function getPalDetails()
    {
        if (!$this->getSession()->getPalDetails()) {
            $api = $this->getApi()
                ->callPalDetails();
            $this->getSession()->setPalDetails($api->getPal());
        }
        return $this->getSession()->getPalDetails();
    }

    /**
     * Initialize payment transaction in case we doing checkout through onepage checkout
     *
     * @param string $paymentAction Preconfigured Payment Action
     * @param Varien_Object $stateObject Object to store order status and state
     * @return Mage_Paypal_Model_Express
     */
    public function initialize($paymentAction, $stateObject)
    {
        $api = $this->getApi();
        if ($this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getBillingAddress();
        } else {
            $address = $this->getQuote()->getShippingAddress();
        }


        $api->setPayment($this->getPayment())
            ->setPaymentType($paymentAction)
            ->setSolutionType($this->getSolutionType())
            ->setAmount($address->getBaseGrandTotal())
            ->setCurrencyCode($this->getQuote()->getBaseCurrencyCode())
            ->setShippingAddress($address)
            ->setInvNum($this->getQuote()->getReservedOrderId());

        $this->_appendAdditionalToApi($address, $api);

        $api->callSetExpressCheckout();

        $this->throwError();

        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_paypal');
        $stateObject->setIsNotified(false);

        Mage::getSingleton('paypal/session')->unsExpressCheckoutMethod();

        return $this;
    }

    /**
     * Authorize
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Express
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $this->placeOrder($payment);
        return $this;
    }

    /**
     * Void payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Express
     */
    public function void(Varien_Object $payment)
    {
        if($payment->getVoidTransactionId()){
            $api = $this->getApi();
            $api->setPayment($payment);
            $api->setAuthorizationId($payment->getVoidTransactionId());
            $this->_wrapApiError($api, $api->callDoVoid());
            $payment->setStatus('SUCCESS')
               ->setCcTransId($api->getTransactionId());
            if ($this->canManageFraud($payment)) {
                $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_DENY);
            }
        } else {
            Mage::throwException(Mage::helper('paypal')->__('Invalid transaction id'));
        }
        return $this;
    }

    /**
     * Online capture
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($payment->getCcTransId()) {
            $api = $this->getApi()
                ->setPaymentType($this->getPaymentAction())
                ->setAmount($amount)
                ->setBillingAddress($payment->getOrder()->getBillingAddress())
                ->setPayment($payment);

            $api->setAuthorizationId($payment->getCcTransId())
                ->setCompleteType('NotComplete');
            $this->_wrapApiError($api, $api->callDoCapture());

            if ($this->canStoreFraud()) {
                $payment->setFraudFlag(true);
            }
            $payment->setStatus('APPROVED');

            if ($api->getAccountStatus()) {
                $payment->setAccountStatus($api->getAccountStatus());
            }
            if ($api->getProtectionEligibility()) {
                $payment->setProtectionEligibility($api->getProtectionEligibility());
            }
            $payment->setTransactionId($api->getTransactionId());
            $payment->setParentTransactionId($payment->getCcTransId());
            $payment->setIsTransactionClosed( !$this->canRefund());

            if ($this->canManageFraud($payment)) {
                $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_ACCEPT);
            }
        } else {
            $this->placeOrder($payment);
        }
        return $this;
    }

    /**
     * Making right API call for current trasaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Express
     */
    public function placeOrder(Varien_Object $payment)
    {
        $api = $this->getApi();

        if ($this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getBillingAddress();
        } else {
            $address = $this->getQuote()->getShippingAddress();
        }

        $api->setAmount($payment->getOrder()->getBaseGrandTotal())
            ->setPaymentType($this->getPaymentAction())
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setInvNum($this->getQuote()->getReservedOrderId());

        $this->_appendAdditionalToApi($payment->getOrder(), $api);

        $this->_wrapApiError($api, $api->callDoExpressCheckoutPayment());
        $payment->setStatus('APPROVED')
            ->setProtectionEligibility($api->getProtectionEligibility())
            ->setPayerId($api->getPayerId());

        if ($this->getQuote()->getPayment()->getAccountStatus()) {
            $payment->setAccountStatus($this->getQuote()->getPayment()->getAccountStatus());
        }

        if ($this->getQuote()->getPayment()->getAddressStatus()) {
            $payment->setAddressStatus($this->getQuote()->getPayment()->getAddressStatus());
        }

        if ($this->getPaymentAction()== Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH) {
            $payment->setCcTransId($api->getTransactionId());
        }
        $payment->setTransactionId($api->getTransactionId());
        $payment->setParentTransactionId($api->getTransactionId());
        $payment->setIsTransactionClosed( !$this->canCapture());

        if ($this->canStoreFraud()) {
            $payment->setFraudFlag(true);
        }
        return $this;
    }

    /**
     * Refund the amount with transaction id
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($payment->getRefundTransactionId() && $amount > 0) {
            $api = $this->getApi();
            $api->setPayment($payment)
                ->setTransactionId($payment->getRefundTransactionId())
                ->setAmount($amount);
            $api->setRefundType($payment->getOrder()->canCreditmemo()
                ? Mage_Paypal_Model_Api_Abstract::REFUND_TYPE_PARTIAL
                : Mage_Paypal_Model_Api_Abstract::REFUND_TYPE_FULL
            );
            $this->_wrapApiError($api, $api->callRefundTransaction());
            $payment->setStatus('SUCCESS')
                ->setCcTransId($api->getTransactionId());

            $payment->setTransactionId($api->getTransactionId());
            $payment->setParentTransactionId($payment->getRefundTransactionId());
            $payment->setIsTransactionClosed(true);

        } else {
            Mage::throwException(Mage::helper('paypal')->__('No refund transaction ID found or wrong amount.'));
        }
        return $this;
    }

    /**
     * Process pending transaction, set status deny or approve
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param string $action
     * @return Mage_Paypal_Model_Express
     */
    public function updateGatewayStatus(Varien_Object $payment, $action)
    {
      if ($payment && $action) {
          if ($payment->getCcTransId()) {
              $transactionId = $payment->getCcTransId();
          } else {
              $transactionId = $payment->getLastTransId();
          }
          $api = $this->getApi();
          $api->setAction($action)
              ->setTransactionId($transactionId)
              ->callManagePendingTransactionStatus();
      }
      return $this;
    }

    /**
     * Cancel payment, if it has fraud status, need to update paypal status
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Express
     */
    public function cancel(Varien_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count() && ($payment->getCcTransId() || $payment->getLastTransId())) {
            if ($payment->getCcTransId()) {
                $payment->setVoidTransactionId($payment->getCcTransId());
            } else {
                $payment->setVoidTransactionId($payment->getLastTransId());
            }
            $this->void($payment);
        }
        parent::cancel($payment);
        return $this;
    }

    /**
     * Add additional fields to Api
     *
     * @param Varien_Object $paymentInfo
     * @param Mage_PayPal_Model_Api_Nvp $api
     * @return Mage_PayPal_Model_Express
     */
    protected function _appendAdditionalToApi($paymentInfo, $api)
    {
        if (is_object($paymentInfo) && is_object($api)) {
            if ($this->getLineItemEnabled()) {
                $api->setLineItems($this->getQuote()->getAllItems())
                    ->setShippingAmount($paymentInfo->getBaseShippingAmount())
                    ->setDiscountAmount($paymentInfo->getBaseDiscountAmount())
                    ->setItemAmount($paymentInfo->getBaseSubtotal())
                    ->setItemTaxAmount($paymentInfo->getTaxAmount());
            }
            if ($this->getFraudFilterStatus()) {
                $api->setReturnFmfDetails(true);
            }
        }
        return $this;
    }


    /**
     * @deprecated after 1.4.0.0-alpha3
     * @see Mage_Paypal_Model_Express_Checkout
     */
    public function shortcutSetExpressCheckout()
    {
        Mage::getSingleton('paypal/express_checkout')->shortcutSetExpressCheckout($this);
        return $this;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * @see Mage_Paypal_Model_Express_Checkout
     */
    public function returnFromPaypal()
    {
        Mage::getSingleton('paypal/express_checkout')->returnFromPaypal($this);
        return $this;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * @see Mage_Paypal_Model_Express_Checkout
     */
    protected function _getExpressCheckoutDetails()
    {
        Mage::getSingleton('paypal/express_checkout')->getExpressCheckoutDetails($this);
        return $this;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * @see Mage_Paypal_Model_Express_Checkout
     */
    public function saveOrder()
    {
        Mage::getSingleton('paypal/express_checkout')->saveOrder($this);
        return $this;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * @see Mage_Paypal_Model_Express_Checkout
     */
    public function updateOrder($orderId)
    {
        Mage::getSingleton('paypal/express_checkout')->updateOrder($orderId, $this);
        return $this;
    }
}
