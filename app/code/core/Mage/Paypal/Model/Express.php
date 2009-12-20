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
    protected $_code  = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;
    protected $_formBlockType = 'paypal/express_form';
    protected $_infoBlockType = 'paypal/express_info';

    /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = true;

    /**
     * Config instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

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
            $paymentAction = Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH;
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
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                $paymentAction = Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
                break;
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
            default:
                $paymentAction = Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
                break;
        }
        return $paymentAction;
    }

    /**
     * Authorize
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Express
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);
        $this->_placeOrder($payment, $amount, $this->getPaymentAction());
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
        if ($authTransactionId = $payment->getParentTransactionId()) {
            $api = $this->getApi();
            $api->setPayment($payment)->setAuthorizationId($authTransactionId);
            $api->callDoVoid();

//            if ($this->canManageFraud($payment)) {
//                $this->updateGatewayStatus($payment, Mage_Paypal_Model_Api_Abstract::ACTION_DENY);
//            }
        } else {
            Mage::throwException(Mage::helper('paypal')->__('Authorization transaction is required to void.'));
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
        parent::capture($payment, $amount);

        // capture basing on an authorized payment
        if ($authTransactionId = $payment->getParentTransactionId()) {
            $api = $this->getApi()
                ->setAuthorizationId($authTransactionId)
                ->setCompleteType($payment->getShouldCloseParentTransaction()
                    ? Mage_Paypal_Model_Config::CAPTURE_TYPE_COMPLETE
                    : Mage_Paypal_Model_Config::CAPTURE_TYPE_NOTCOMPLETE
                )
                ->setAmount($amount)
                ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
                ->setInvNum($payment->getOrder()->getIncrementId())
                // TODO: pass 'NOTE' to API
            ;
            $api->callDoCapture();

            // add capture transaction info
            $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(false);
            // collect additional information TODO

//            if ($this->canStoreFraud()) {
//                $payment->setFraudFlag(true);
//            }
//            if ($this->canManageFraud($payment)) {
//                $this->updateGatewayStatus($payment, Mage_Paypal_Model_Config::FRAUD_ACTION_ACCEPT);
//            }
        }
        // sale (auth & capture)
        else {
            return $this->_placeOrder($payment, $amount, Mage_Paypal_Model_Config::PAYMENT_ACTION_SALE);
        }

        return $this;
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @param string $paymentAction
     */
    protected function _placeOrder($payment, $amount, $paymentAction = Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH)
    {
        // prepare api call
        $order = $payment->getOrder();
        $token = $payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        $api = $this->getApi()
            ->setToken($token)
            ->setPayerId($payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))
            ->setAmount($amount)
            ->setPaymentType($paymentAction) // TODO: refactor payment_type in API
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/express'))
            ->setInvNum($order->getIncrementId())
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setSubtotalAmount($order->getBaseSubtotal())
            ->setShippingAmount($order->getBaseShippingAmount())
            ->setTaxAmount($order->getBaseTaxAmount())
            ->setDiscountAmount($order->getBaseDiscountAmount())
        ;

        // add line items
        if ($this->getLineItemEnabled()) {
            $api->setLineItems($order->getAllItems());
        }
        if ($this->getFraudFilterStatus()) {
            $api->setReturnFmfDetails(true);
        }

        // call api and get details from it
        $api->callDoExpressCheckoutPayment();
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0);
        // TODO accumulate additional info
//if ($this->canStoreFraud()) {
//    $payment->setFraudFlag(true);
//}
//if ($this->canManageFraud($payment)) {
//    $this->updateGatewayStatus($payment, Mage_Paypal_Model_Config::FRAUD_ACTION_ACCEPT);
//}
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
        $captureTxnId = $payment->getParentTransactionId();
        if ($captureTxnId) {
            $api = $this->getApi();
            $api->setPayment($payment)
                ->setTransactionId($captureTxnId)
                ->setAmount($amount)
                ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ;
            $canRefundMore = $payment->getOrder()->canCreditmemo(); // TODO: fix this to be able to create multiple refunds
            $api->setRefundType($canRefundMore ? Mage_Paypal_Model_Config::REFUND_TYPE_PARTIAL
                : Mage_Paypal_Model_Config::REFUND_TYPE_FULL
            );
            $api->callRefundTransaction();
            $payment->setTransactionId($api->getTransactionId())
                ->setIsTransactionClosed(1) // refund initiated by merchant
                ->setShouldCloseParentTransaction(!$canRefundMore)
            ;

        } else {
            Mage::throwException(Mage::helper('paypal')->__('Capture transaction is required to refund.'));
        }
        return $this;
    }

    /**
     * Process pending transaction, set status deny or approve
     *
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
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $this->void($payment);
        }
        return parent::cancel($payment);
    }

    /**
     * Prepare initial state before placing payment
     * @param unknown_type $paymentAction
     * @param unknown_type $stateObject
     * ?
     */
    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
        return parent::initialize($paymentAction, $stateObject);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see Mage_Checkout_OnepageController::savePaymentAction()
     * @see Mage_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypal/express/start');
    }

    /**
     * Config instance setter
     * @param Mage_Paypal_Model_Config $instance
     * @return Mage_Paypal_Model_Express
     */
    public function setConfig(Mage_Paypal_Model_Config $instance)
    {
        if (Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS !== $instance->getMethodCode()) {
            throw new Exception('Config instance is not valid for this payment method.');
        }
        $this->_config = $instance;
        return $this;
    }
}
