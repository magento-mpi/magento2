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

class Mage_AmazonPayments_Model_Payment_Asp extends Mage_AmazonPayments_Model_Payment_Asp_Abstract
{

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true; 
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = true;
    
    protected $_formBlockType = 'amazonpayments/asp_form'; // INTERFASE
	protected $_code  = 'amazonpayments_asp'; // INTERFASE
	protected $_order;

    public function isAvailable($quote=null) // INTERFASE
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/active');
    }
		
	public function getApi()
    {
        return Mage::getSingleton('amazonpayments/api_asp');
    }
        
    public function getNotification()
    {
        return Mage::getSingleton('amazonpayments/payment_asp_notification');
    }
    
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }    
    
    public function getOrder()
    {

        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')->loadByIncrementId(
                $paymentInfo->getOrder()->getRealOrderId()
            );
        }
        return $this->_order;
        /*
        if (!$this->_order) {
            $session = Mage::getSingleton('checkout/session');
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            $this->_order = $order;         
        }
        return $this->_order;
        */
    }    

    // PAY
    
    public function getOrderPlaceRedirectUrl() // INTERFASE
    {
        return Mage::getUrl('amazonpayments/asp/pay');
    }

    public function getPayRedirectUrl()
    {
        return $this->getApi()->getPayUrl();
    }

    public function getPayRedirectParams()
    {
        $orderId = $this->getOrder()->getRealOrderId();
        $amount = Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseGrandTotal());
        $currencyCode = $this->getOrder()->getBaseCurrency();
        return $this->getApi()->getPayParams($orderId, $amount, $currencyCode);   
    }

    public function processEventRedirect()
    {
        $this->getOrder()->addStatusToHistory(
           $this->getOrder()->getStatus(), 
           Mage::helper('amazonpayments')->__('Customer was redirected to Amazon Simple Pay')
        )->save();
    }    

    public function processEventReturnSuccess()
    {
    	$this->getOrder()->addStatusToHistory(
           $this->getOrder()->getStatus(), 
           Mage::helper('amazonpayments')->__('Customer successfully returned from Amazon Simple Pay')
        )->save();
    }    
    
    public function processEventReturnCancel()
    {
        $this->getOrder()->setState(
            Mage_Sales_Model_Order::STATE_CANCELED, 
            true,
            Mage::helper('amazonpayments')->__('Customer canceled payment and successfully returned from Amazon Simple Pay'),
            $notified = false
        )->save();        
    }    

    public function initialize($paymentAction, $stateObject) // INTERFASE
    {
        $state = Mage_Sales_Model_Order::STATE_NEW;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage::getSingleton('sales/order_config')->getStateDefaultStatus($state));
        $stateObject->setIsNotified(false);
    }    
    
    
   // NOTIFICATION 
    
    public function processNotification($requestParams)
    {
    	$this->getNotification()
    	   ->setPayment($this)
    	   ->process($requestParams);
    }

   // CAPTURE

    public function capture(Varien_Object $payment, $amount) // INTERFASE
    {
        if (is_null($payment->getCcTransId())) {
            throw new Exception(Mage::helper('amazonpayments')->__('Notification of successful reservation was received. We can not hold confirmation online'), 111);
        }
    }

    public function processInvoice($invoice, $payment) // INTERFASE
    {
        if (!is_null($payment->getCcTransId()) && 
            is_null($payment->getLastTransId()) &&    
            is_null($invoice->getTransactionId())) {
            	
            $amount = Mage::app()->getStore()->roundPrice($invoice->getBaseGrandTotal());
            $currencyCode = $payment->getOrder()->getBaseCurrency();        
            $transactionId = $payment->getCcTransId();
            $response = $this->getApi()->capture($transactionId, $amount, $currencyCode);

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_ERROR) {
                /*pr*/if(1){echo('<div style="border:1px solid #000">'.__FILE__.':'.__LINE__.'<pre>');
                print_r($response);  echo('</pre></div>');}
                die();
            	throw new Exception(Mage::helper('amazonpayments')->__('response error'), 111);            
            }
            
            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_SUCCESS ||
                $response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_PENDING) {

                $payment->setForcedState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
                //$payment->setLastTransId($response->getTransactionId());
                
                $invoice->setTransactionId($response->getTransactionId());      
                $invoice->addComment(Mage::helper('amazonpayments')->__('Init capture action to Amazon Simple Pay service'));

                $payment->getOrder()->addStatusToHistory(
                  $payment->getOrder()->getStatus(), 
                  Mage::helper('amazonpayments')->__('Init capture action to Amazon Simple Pay service, and create invoice')
                )->save();
                
            }
        }
    }    
    
   // REFUND
    
//                $this->getMethodInstance()->processBeforeRefund($creditmemo->getInvoice(), $this);
//                $this->getMethodInstance()->refund($this, $creditmemo->getBaseGrandTotal());
//                $this->getMethodInstance()->processCreditmemo($creditmemo, $this);    
    
    public function canCapturePartial() // INTERFASE
    {
    	return false;
    	if ($this->getOrder()->getState() != Mage_Sales_Model_Order::STATE_PROCESSING) {
            return false;   
        }
        return true;
    }

    public function refund_1111(Varien_Object $payment, $amount)
    {
        die($amount);
        $hlp = Mage::helper('googlecheckout');

//        foreach ($payment->getCreditMemo()->getCommentsCollection() as $comment) {
//            $this->setReason($hlp->__('See Comments'));
//            $this->setComment($comment->getComment());
//        }

        $reason = $this->getReason() ? $this->getReason() : $hlp->__('No Reason');
        $comment = $this->getComment() ? $this->getComment() : $hlp->__('No Comment');

        $api = Mage::getModel('googlecheckout/api')->setStoreId($payment->getOrder()->getStoreId());
        $api->refund($payment->getOrder()->getExtOrderId(), $amount, $reason, $comment);

        return $this;
    }  
    
    
    public function processCreditmemo($creditmemo, $payment)
    {
    	
    	$transactionId = $creditmemo->getInvoice()->getTransactionId();
    	
    	if (!is_null($transactionId)) {
    		
    		$amount = Mage::app()->getStore()->roundPrice($creditmemo->getBaseGrandTotal());
            $currencyCode = $payment->getOrder()->getBaseCurrency();        
            $response = $this->getApi()->refund($transactionId, $amount, $currencyCode, '#444008' . time());

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_ERROR) {
                /*pr*/if(1){echo('<div style="border:1px solid #000">'.__FILE__.':'.__LINE__.'<pre>');
                print_r($response);  echo('</pre></div>');}
                die();
            	throw new Exception(Mage::helper('amazonpayments')->__('response error'), 111);            
            }
            
            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_SUCCESS ||
                $response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_PENDING) {

                //$payment->setForcedState(Mage_Sales_Model_Order_Creditmemo::STATE_OPEN);
                
                $creditmemo->setTransactionId($response->getTransactionId());      
                $creditmemo->addComment(Mage::helper('amazonpayments')->__('Init refund action to Amazon Simple Pay service'));
                $creditmemo->setState(Mage_Sales_Model_Order_Creditmemo::STATE_OPEN);
                
                $payment->getOrder()->addStatusToHistory(
                  $payment->getOrder()->getStatus(), 
                  Mage::helper('amazonpayments')->__('Init refund action to Amazon Simple Pay service, and create creditmemo')
                )->save();
            }
        }

    }
    
    public function canRefund()
    {
        return true;
    }
     
    
    
    
    
        
        
        
        
        
    
    
    
    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        die('authorize');  
    }

 
    
    
    
    
    
    
    
    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        /*pr*/if(1){echo('<div style="border:1px solid #000">'.__FILE__.':'.__LINE__.'<pre>');
        print_r($amount);  echo('</pre></div>');}
        
        /*pcn*/if(1){echo('<div style="border:1px solid #000">'.__FILE__.':'.__LINE__.'<pre>');
        print_r(get_class($payment));  echo('</pre></div>');}

        die('cancel');  
    
    }

    /**
     * Void payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
    	die('void');
        return $this;
    }
        
    
   /* public function canCapture()
    {
        return true;

        if (is_null($this->getOrder()->getPayment()->getCcTransId())) {
    		return false;
    	}
        return true;
    }*/
    

    public function canVoid(Varien_Object $payment)
    {
        return false;
    }
    
}
