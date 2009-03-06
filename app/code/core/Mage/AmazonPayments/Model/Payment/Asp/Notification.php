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

class Mage_AmazonPayments_Model_Payment_Asp_Notification extends Varien_Object
{
	const EXCEPTION_ORDER_NOT_FOUND = 10021;
    const EXCEPTION_FAILED_CURRENCY_CODE = 10022;
    const EXCEPTION_FAILED_PAYMENT_METHOD = 10023;
    const EXCEPTION_VIOLATION_SEQUENCE_STATES = 10024;

    protected $_payment;
    
    public function setPayment($payment)
    {
    	$this->_payment = $payment;
        return $this;
    }

    public function getPayment()
    {
        return $this->_payment;
    }
    
    // PROCESS STATUSES
    
    public function process($requestParams)
    {
        try {
	    	$request = $this->getPayment()->getApi()->processNotification($requestParams);
	        $order = $this->_getRequestOrder($request); 
	        	        
	        switch ($request->getStatus()) {
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_CANCEL:
	                $this->_processCancel($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_RESERVE_SUCCESSFUL: 
	            	$this->_processReserveSuccess($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_PAYMENT_INITIATED: 
	            	$this->_processPaymetInitiated($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_PAYMENT_SUCCESSFUL: 
	                $this->_processPaymentSuccessful($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_PAYMENT_FAILED: 
	                $this->_processPaymentFailed($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_REFUND_SUCCESSFUL: 
	                $this->_processRefundSuccessful($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_REFUND_FAILED: 
	                $this->_processRefundFailed($request, $order); 
	                break;         
	            case Mage_AmazonPayments_Model_Api_Asp_Ipn_Request::STATUS_SYSTEM_ERROR: 
	                $this->_processSystemError($request, $order); 
	                break;
	        }         
        
        } catch(Exception $e) {
        	$this->_catchExeption($e);
        }
        
        $order->save();
    }
    
    protected function _processCancel($request, $order) // OK
    {
    	if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
	       $order->addStatusToHistory(
	           $order->getStatus(), 
	           Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed canceled this payment')
	        );
            return true;
        }
    	
    	if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
	       $order->setState(
	            Mage_Sales_Model_Order::STATE_CANCELED, 
	            true,
	            Mage::helper('amazonpayments')->__('Payment was canceled'),
	            $notified = false
	        );        
            return true;    	   
    	}

        $this->_throwExeptionViolationSequenceStates();
    } 
    
    protected function _processReserveSuccess($request, $order) // OK 
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW) {
            $this->_throwExeptionViolationSequenceStates();
        }

        $order->getPayment()->setCcTransId($request->getTransactionId());
                
        $order->setState(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 
            true,
            Mage::helper('amazonpayments')->__('Payment was authorized'),
            $notified = false
        );        
    } 
    
    protected function _processPaymetInitiated($request, $order) // OK
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $this->_throwExeptionViolationSequenceStates();
        }
        $order->setState(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 
            true,
            Mage::helper('amazonpayments')->__('Payment was initiated'),
            $notified = false
        );        
    } 
    
    protected function _processPaymentSuccessful($request, $order)
    {
    	if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $this->_throwExeptionViolationSequenceStates();
        }

        $msg = '';
        
        if (!$invoice = $this->_getOrderInvoice($order)) {

        	$orderAmount = Mage::app()->getStore()->roundPrice($order->getBaseGrandTotal());
            $requestAmount = Mage::app()->getStore()->roundPrice($request->getAmount());
            if ($orderAmount != $requestAmount) {
            	// exeption
            }
        	        	
        	$invoice = $order->prepareInvoice();
	        $invoice->register()->pay();
            $invoice->addComment(Mage::helper('amazonpayments')->__('Auto-generated from Amazon Simple Pay charge'));
            $invoice->setTransactionId($request->getTransactionId());
	        
            $transactionSave = Mage::getModel('core/resource_transaction')
	            ->addObject($invoice)
	            ->addObject($invoice->getOrder())
	            ->save();

            $msg = $msg . Mage::helper('amazonpayments')->__('Invoice #%s automatically created', $invoice->getIncrementId());
        
        } else {
            if ($invoice->getTransactionId() != $request->getTransactionId()) {
                $order->addStatusToHistory($order->getStatus(), 'id != id:' . $invoice->getTransactionId());            	
            	//exeption 
            }	
            
            $invoiceAmount = Mage::app()->getStore()->roundPrice($invoice->getGrandTotal());
            $requestAmount = Mage::app()->getStore()->roundPrice($request->getAmount());
            if ($invoiceAmount != $requestAmount) {
                $order->addStatusToHistory($order->getStatus(), 'sum != sum:');                
            	//exeption 
            }   
            
            switch ($invoice->getState())
            {
            	case Mage_Sales_Model_Order_Invoice::STATE_OPEN:
                    $invoice->addComment(Mage::helper('amazonpayments')->__('Auto capture from Amazon Simple Pay charge'));
                    $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID)->save();
                    $msg = $msg . Mage::helper('amazonpayments')->__('Invoice #%s automatically capture', $invoice->getIncrementId());
            		break;

            	case Mage_Sales_Model_Order_Invoice::STATE_PAID:
                    $invoice->addComment(Mage::helper('amazonpayments')->__('Amazon Simple Pay service confirmed payment this invoice'));
                    $msg = $msg . Mage::helper('amazonpayments')->__('Confirmed payment invoice #%s', $invoice->getIncrementId());
            		break;
            }
            
        }
        
        $order->getPayment()->getLastTransId($request->getTransactionId());    
        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->setState(
            Mage_Sales_Model_Order::STATE_PROCESSING, 
            true,
            Mage::helper('amazonpayments')->__('Payment was success'),
            $notified = true
        );
            
        return true;    
        
    } 
    
    protected function _processPaymentFailed($request, $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_NEW &&
            $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $this->_throwExeptionViolationSequenceStates();
        }

        $order->setState(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 
            Mage_AmazonPayments_Model_Payment_Asp::STATUS_SUCCESS,
            Mage::helper('amazonpayments')->__('Payment was failed'),
            $notified = false
        );        
    } 
    
    protected function _processRefundSuccessful($request, $order) 
    {
        
    } 
    
    protected function _processRefundFailed($request, $order)
    {
        
    }

    protected function _processSystemError($request, $order)
    {
        $order->setState(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 
            Mage_AmazonPayments_Model_Payment_Asp::STATUS_SUCCESS,
            Mage::helper('amazonpayments')->__('Amazon Simple Pay service is not available. Payment was not processed.'),
            $notified = false
        );        
    } 

    
    protected function _getRequestOrder($request)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($request->getReferenceId());
        
        if ($order->isEmpty()) {
            throw new Exception(
                Mage::helper('amazonpayments')->__('Order specified in the IPN request can not be found'), 
                self::EXCEPTION_ORDER_NOT_FOUND
            );
        }
        
        if ($order->getPayment()->getMethodInstance()->getCode() != $this->getPayment()->getCode()) {
            throw new Exception(
                Mage::helper('amazonpayments')->__('Payment method order is not Amazon Simple Pay'), 
                self::EXCEPTION_FAILED_PAYMENT_METHOD
            );
        }
        
        if ($order->getBaseCurrency()->getCurrencyCode() != $request->getCurrencyCode()) {
            throw new Exception(
                Mage::helper('amazonpayments')->__('Currency does not match the currency of the order INP request'), 
                self::EXCEPTION_FAILED_CURRENCY_CODE
            );
        }
        
        return $order;
    }   

    protected function _getOrderInvoice($order)
    {
        foreach ($order->getInvoiceCollection() as $orderInvoice) {
            if ($orderInvoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID ||
                $orderInvoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_OPEN) {
                return $orderInvoice;
            }
        }
        
        return false;
    }
    
    protected function _throwExeptionViolationSequenceStates()
    {
    	throw new Exception(
            Mage::helper('amazonpayments')->__('Violation of the sequence of states in order'), 
            self::EXCEPTION_VIOLATION_SEQUENCE_STATES
        );
    }

    protected function _catchExeption($exeption)
    {

    	switch ($exeption->getCode()) {
            case Mage_AmazonPayments_Model_Api_Asp::EXCEPTION_INVALID_SIGN_REQUEST:
                die($exeption->getMessage());
                break;
            case Mage_AmazonPayments_Model_Api_Asp::EXCEPTION_INVALID_IPN_REQUEST:
                die($exeption->getMessage());
                break;
    		case self::EXCEPTION_ORDER_NOT_FOUND:
        		die($exeption->getMessage());
        		break;
            case self::EXCEPTION_FAILED_CURRENCY_CODE:
                die($exeption->getMessage());
                break;
            case self::EXCEPTION_FAILED_PAYMENT_METHOD:
                die($exeption->getMessage());
                break;
            case self::EXCEPTION_VIOLATION_SEQUENCE_STATES:
                die($exeption->getMessage());
                break;
            default:    
                die($exeption->getMessage());
    	}
    }
}
