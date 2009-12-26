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

    /**
     * Config instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * API instance
     *
     * @var Mage_Paypal_Model_Api_Nvp
     */
    protected $_api = null;

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getConfig()->isCurrencyCodeSupported($currencyCode);
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
     * API instance getter
     * Sets current store id to current config instance and passes it to API
     *
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel('paypal/api_nvp');
        }
        $this->getConfig(); // make sure config is instantiated
        $this->_api->setConfigObject($this->_config->setStoreId($this->getStore()));
        return $this->_api;
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
     * Payment action getter compatible with payment model
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     * @see Mage_Paypal_Model_Express::getConfigPaymentAction() TODO: remove copypaste
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfig()->getPaymentAction();
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
        if ($this->_config->lineItemsEnabled) {
            list($items, $totals) = Mage::helper('paypal')->prepareLineItems($order);
            $this->_api->setLineItems($items)->setLineItemTotals($totals);
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
            Mage::throwException(Mage::helper('paypal')->__('Impossible to issue a refund transaction, because capture transaction does not exist.'));
        }
        return $this;
    }

//    /**
//     * Process pending transaction, set status deny or approve
//     *
//     * @param Mage_Sales_Model_Order_Payment $payment
//     * @param string $action
//     * @return Mage_Paypal_Model_Express
//     */
//    public function updateGatewayStatus(Varien_Object $payment, $action)
//    {
//      if ($payment && $action) {
//          if ($payment->getCcTransId()) {
//              $transactionId = $payment->getCcTransId();
//          } else {
//              $transactionId = $payment->getLastTransId();
//          }
//          $api = $this->getApi();
//          $api->setAction($action)
//              ->setTransactionId($transactionId)
//              ->callManagePendingTransactionStatus();
//      }
//      return $this;
//    }

    /**
     * Cancel payment
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

    /**
     * Config instance getter
     * @return Mage_Paypal_Model_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = Mage::getModel('paypal/config', array($this->_code, (int)$this->getStore()));
        }
        return $this->_config;
    }
}
