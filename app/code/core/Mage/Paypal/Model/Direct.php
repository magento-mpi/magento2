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
 * PayPal Direct Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'paypal_direct';
    protected $_formBlockType = 'paypal/direct_form';
    protected $_infoBlockType = 'paypal/payment_info';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    /**
     * Config instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @param string $paymentAction
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount, $paymentAction = Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH)
    {
        $order = $payment->getOrder();
        $api = $this->getApi()
            ->setPaymentType($paymentAction)
            ->setIpAddress(Mage::app()->getRequest()->getClientIp(false))
            // fmf auto
            ->setAmount($amount)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($order->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/direct'))
//->setSubtotalAmount($order->getBaseSubtotal())
//->setShippingAmount($order->getBaseShippingAmount())
//->setTaxAmount($order->getBaseTaxAmount())
            ->setCreditCardType($payment->getCcType())
            ->setCreditCardNumber($payment->getCcNumber())
            ->setCreditCardExpirationDate(sprintf('%02d%02d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
            ->setCreditCardCvv2($payment->getCcCid())
//            ->setMaestroSoloIssueDate()
//            ->setMaestroSoloIssueNumber()
//        'STARTDATE'      => 'maestro_solo_issue_date', // MMYYYY, always six chars, including leading zero
//        'ISSUENUMBER'    => 'maestro_solo_issue_number',

//        'AUTHSTATUS3D' => 'centinel_authstatus',
//        'MPIVENDOR3DS' => 'centinel_mpivendor',
//        'CAVV'         => 'centinel_cavv',
//        'ECI3DS'       => 'centinel_eci',
//        'XID'          => 'centinel_xid',

        ;
        // add shipping address
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
        }

        // add line items
        if ($this->_config->lineItemsEnabled) {
            list($items, $totals) = Mage::helper('paypal')->prepareLineItems($order);
            $api->setLineItems($items)->setLineItemTotals($totals);
        }

        // call api and import transaction and other payment information
        $api->callDoDirectPayment();
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0)
            ->setIsPaid($api->isPaid($api->getPaymentStatus()))
        ;
        Mage::getModel('paypal/info')->importToPayment($api, $payment);
    }








// Everything below is copypaste from Mage_Paypal_Model_Express. To fix this, this class must be extended from some abstract WPP model

    /**
     * Authorize
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Mage_Paypal_Model_Direct
     * @see Mage_Paypal_Model_Express::authorize() TODO: remove copypaste
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);
        $this->_placeOrder($payment, $amount);
        return $this;
    }

    /**
     * Void authorization
     * @param Varien_Object $payment
     * @return Mage_Paypal_Model_Direct
     * @see Mage_Paypal_Model_Express::void() TODO: remove copypaste
     */
    public function void(Varien_Object $payment)
    {
        if ($authTransactionId = $payment->getParentTransactionId()) {
            $api = $this->getApi();
            $api->setPayment($payment)->setAuthorizationId($authTransactionId);
            $api->callDoVoid();
            Mage::getModel('paypal/info')->importToPayment($api, $payment);
        } else {
            Mage::throwException(Mage::helper('paypal')->__('Authorization transaction is required to void.'));
        }
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @see Mage_Paypal_Model_Express::cancel() TODO: remove copypaste
     */
    public function cancel(Varien_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $this->void($payment);
        }
        return parent::cancel($payment);
    }

    /**
     * Online capture
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     * @see Mage_Paypal_Model_Express::capture() TODO: remove copypaste
     */
    public function capture(Varien_Object $payment, $amount)
    {
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
            Mage::getModel('paypal/info')->importToPayment($api, $payment);
        } else {
            $this->_placeOrder($payment, $amount, Mage_Paypal_Model_Config::PAYMENT_ACTION_SALE);
        }
        return $this;
    }

    /**
     * Refund the amount with transaction id
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     * @see Mage_Paypal_Model_Express::refund() TODO: remove copypaste
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $captureTxnId = $payment->getParentTransactionId();
        if ($captureTxnId) {
            $api = $this->getApi();
            $order = $payment->getOrder();
            $api->setPayment($payment)
                ->setTransactionId($captureTxnId)
                ->setAmount($amount)
                ->setCurrencyCode($order->getBaseCurrencyCode())
            ;
            $canRefundMore = $order->canCreditmemo(); // TODO: fix this to be able to create multiple refunds
            $isFullRefund = !$canRefundMore
                && (0 == ((float)$order->getBaseTotalOnlineRefunded() + (float)$order->getBaseTotalOfflineRefunded()));
            $api->setRefundType($isFullRefund ? Mage_Paypal_Model_Config::REFUND_TYPE_FULL
                : Mage_Paypal_Model_Config::REFUND_TYPE_PARTIAL
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

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     * @see Mage_Paypal_Model_Express::canUseForCurrency() TODO: remove copypaste
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getConfig()->isCurrencyCodeSupported($currencyCode);
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
     * Config instance getter
     *
     * @return Mage_Paypal_Model_Config
     * @see Mage_Paypal_Model_Express::getConfig() TODO: remove copypaste
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $params = array($this->_code);
            if ($this->getStore()) {
                $params[] = (int)$this->getStore();
            }
            $this->_config = Mage::getModel('paypal/config', $params);
        }
        return $this->_config;
    }

    /**
     * API instance getter
     * Sets current store id to current config instance and passes it to API
     *
     * @return Mage_Paypal_Model_Api_Nvp
     * @see Mage_Paypal_Model_Express::getApi() TODO: remove copypaste
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
}
