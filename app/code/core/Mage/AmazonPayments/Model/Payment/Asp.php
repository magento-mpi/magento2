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

class Mage_AmazonPayments_Model_Payment_Asp extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment module of Checkout by Amazon
     * CBA - Checkout By Amazon
     */

    protected $_code  = 'amazonpayments_asp';
    protected $_formBlockType = 'amazonpayments/asp_form';
    protected $_api;

    const ACTION_AUTHORIZE = 0;
    const ACTION_AUTHORIZE_CAPTURE = 1;
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';

    /**
     * Return true if the method can be used at this time
     *
     * @return bool
     */
    public function isAvailable($quote=null)
    {
        return Mage::getStoreConfig('payment/amazonpayments_asp/active');
    }

    /**
     * Get AmazonPayments API Model
     *
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function getApi()
    {
        if (!$this->_api) {
        	$this->_api = Mage::getSingleton('amazonpayments/api_asp');
            $this->_api->setPaymentCode($this->getCode());
        }
        return $this->_api;
    }
    
    public function getOrderPlaceRedirectUrl()
    {
        $orderId = $this->getQuote()->getReservedOrderId();
        $amount = Mage::app()->getStore()->roundPrice($this->getQuote()->getBaseGrandTotal());
        $currencyCode = $this->getQuote()->getBaseCurrencyCode();
    	return $this->getApi()->getPayNowRedirectUrl($orderId, $amount, $currencyCode);   
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function processIpnRequest($requestParams)
    {
    	$ipnRequest = $this->getApi()->getIpnRequest($requestParams);
    	
    	if (!$ipnRequest) {
            echo('Request is failed');
        }
        
        $order = $this->_getIpnRequestOrder($ipnRequest); 
        
        if (!$order) {
            echo('Request is not valid for order');
        } 
        
        echo('sdgf');
        echo(Mage_Sales_Model_Order::STATE_PROCESSING);
        echo(Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::IPN_STATUS_RESERVE_SUCCESSFUL);
        
        echo('FFF');
        die();

        
        
        
/*        switch ($ipnRequestParams['status']) {
            case $apiClassName::IPN_STATUS_CANCEL:
            	$this->_processIpnCancel(); 
            	break;         
            case $apiClassName::IPN_STATUS_RESERVE_SUCCESSFUL: 
            	$this->_processIpnReserveSuccess(); 
            	break;         
            case $apiClassName::IPN_STATUS_PAYMENT_INITIATED: 
            	$this->_processIpnPaymetInitiated(); 
            	break;         
            case $apiClassName::IPN_STATUS_PAYMENT_SUCCESSFUL: 
            	$this->_processIpnPaymentSuccessful(); 
            	break;         
            case $apiClassName::IPN_STATUS_PAYMENT_FAILED: 
            	$this->_processIpnPaymentFailed(); 
            	break;         
            case $apiClassName::IPN_STATUS_REFUND_SUCCESSFUL: 
            	$this->_processIpnRefundSuccessful(); 
            	break;         
            case $apiClassName::IPN_STATUS_REFUND_FAILED: 
            	$this->_processIpnRefundFailed(); 
            	break;         
            case $apiClassName::IPN_STATUS_SYSTEM_ERROR: 
            	$this->_processIpnSystemError(); 
            	break;         
        }*/
       
        
       $order->setState(
                            Mage_Sales_Model_Order::STATE_PROCESSING, 'amazon_asp_pay_wait',
                            Mage::helper('amazonpayments')->__('comment to customer'),
                            $notified = true
                        );        

                  $order->addStatusToHistory(
                        $order->getStatus(),//continue setting current order status
                        Mage::helper('amazonpayments')->__('Comment to history')
                    );
                    
                    $order->save();
                    $order->sendNewOrderEmail();
                        
echo ('Ok');
        
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */    
    private function _getIpnRequestOrder($ipnRequest)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($ipnRequest->getReferenceId());
        if ($order->isEmpty()) {
            echo('Order for IPN not found');
            return false;
        }
        if ($order->getPayment()->getMethodInstance()->getCode() != $this->getCode()) {
            echo('Order not pay ASP');
            return false;
        }
        if ($order->getBaseCurrency()->getCurrencyCode() != $ipnRequest->getCurrencyCode()) {
            echo('Order currency code not currency IPN');
            return false;
        }
        return $order;
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
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get AmazonPayments session namespace
     *
     * @return Mage_AmazonPayments_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('amazonpayments/session');
    }

    /**
     * Getting amazonpayments_cba action url
     *
     * @return string
     */
    public function getPaymentAction()
    {
    	$paymentAction = Mage::getStoreConfig('payment/amazonpayments_asp/payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_AmazonPayments_Model_Api::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function canCapture()
    {
        return true;
    }

    /**
     * initialize payment transaction in case
     * we doing checkout through onepage checkout
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this;
    	
    	$_quote = $this->getCheckout()->getQuote();
        $address = $_quote->getBillingAddress();

        $this->getApi()
            ->setPaymentType($paymentAction)
            ->setAmount($address->getBaseGrandTotal())
            ->setCurrencyCode($_quote->getBaseCurrencyCode())
            ->setBillingAddress($address)
            ->setCardId($_quote->getReservedOrderId())
            ->setCustomerName($_quote->getCustomer()->getName());
            #->callSetExpressCheckout();

        #$this->throwError();

        $stateObject->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
        $stateObject->setStatus('Processing');
        $stateObject->setIsNotified(false);

        Mage::getSingleton('amazonpayments/session')->unsExpressCheckoutMethod();

        return $this;
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Processing error from amazon
     *
     * @return Mage_AmazonPayments_Model_Payment_Cba
     */
    public function catchError()
    {
        if ($this->getApi()->getError()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    $s->addError(Mage::helper('amazonpayments')->__('There was an error connecting to the Amazon server: %s', $e['message']));
                    break;

                case 'API':
                    $s->addError(Mage::helper('amazonpayments')->__('There was an error during communication with Amazon: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }

    /**
     * Works same as catchError method but instead of saving
     * error message in session throws exception
     *
     * @return Mage_AmazonPayments_Model_Payment_Cba
     */
    public function throwError()
    {
        if ($this->getApi()->getError()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    Mage::throwException(Mage::helper('amazonpayments')->__('There was an error connecting to the Amazon server: %s', $e['message']));
                    break;

                case 'API':
                    Mage::throwException(Mage::helper('amazonpayments')->__('There was an error during communication with Amazon: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }
}