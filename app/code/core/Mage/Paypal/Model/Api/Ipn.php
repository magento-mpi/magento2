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
 * IPN wrapper model
 */
class Mage_Paypal_Model_Api_Ipn extends Mage_Paypal_Model_Api_Abstract
{
    /*
     * @param Mage_Sales_Model_Order
     */
    protected $_order = null;

    /**
     * return paypal sandbox url, depending of sendbox flag.
     * used for redirect to paypal, express method
     *
     * @return string
     */
    public function getPaypalUrl()
    {
         if ($this->getSandboxFlag()) {
             $url='https://www.sandbox.paypal.com/cgi-bin/webscr';
         } else {
             $url='https://www.paypal.com/cgi-bin/webscr';
         }
         return $url;
    }

    /**
     * Get ipn data, send verification to PayPal, run corresponding handler    *
     *
     * @return Mage_Paypal_Model_Api_Ipn
     */
    public function processIpnRequest()
    {
        if (!$this->getIpnFormData()) {
            return $this;
        }

        $sReq = '';
        $sReqDebug = '';
        foreach($this->getIpnFormData() as $k=>$v) {
            $sReq .= '&'.$k.'='.urlencode(stripslashes($v));
            $sReqDebug .= '&'.$k.'=';
        }
        //append ipn commdn
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);

        $http = new Varien_Http_Adapter_Curl();
        $http->write(Zend_Http_Client::POST, $this->getPaypalUrl(), '1.1', array(), $sReq);
        $response = $http->read();
        if ($this->getDebug()) {
            $debug = Mage::getModel('paypal/api_debug')
                    ->setApiEndpoint($this->getPaypalUrl())
                    ->setRequestBody($sReq)
                    ->setResponseBody($response)
                    ->save();
        }

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($response=='VERIFIED') {
            $this->processIpnVerified();
        } else {
            $this->processIpnFail();
        }
        return $this;
    }

    /**
     * Return loade order by increment id
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            $id = $this->getIpnFormData('invoice');
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($id);
            if (!$order->getId()) {
                return null;
            } else {
                $this->_order = $order;
            }
        }
        return $this->_order;
    }

    /**
     * Validate incoming data: check amount, check transaction id
     *
     * @return bool
     */
    protected function _verifyData()
    {
        if ($this->getIpnFormData('mc_gross')!=$this->_getOrder()->getBaseGrandTotal()) {
            return Mage::helper('paypal')->__('Order total amount does not match PayPal gross total amount');
        } elseif ($this->getIpnFormData('txn_id') != $this->_getOrder()->getPayment()->getLastTransId()
            && $this->getIpnFormData('txn_id') != $this->_getOrder()->getPayment()->getCcTransId()) {
            return Mage::helper('paypal')->__('Order transaction id does not match PayPal transaction id');
        } else {
            return false;
        }
    }

    /**
     * Made acction according workflow for verified == true
     *
     * @return Mage_Paypal_Model_Api_Ipn
     */
    public function processIpnVerified()
    {
        //when verified need to convert order into invoice
        $order = $this->_getOrder();

        if (!$order->getId()) {
            /*
            * need to have logic when there is no order with the order id from paypal
            */

        } else {
            if ($errorMessage = $this->_verifyData()) {
                //when grand total does not equal, need to have some logic to take care
                $order->addStatusToHistory(
                    $order->getStatus(),//continue setting current order status
                    $errorMessage
                );
                $order->save();
            } else {
                /*
                if payer_status=verified ==> transaction in sale mode
                if transactin in sale mode, we need to create an invoice
                otherwise transaction in authorization mode
                */
                if ($this->getIpnFormData('payment_status') == 'Completed') {
                   if (!$order->canInvoice()) {
                       //when order cannot create invoice, need to have some logic to take care
                       $order->addStatusToHistory(
                            $order->getStatus(), // keep order status/state
                            Mage::helper('paypal')->__('Error in creating an invoice', true),
                            $notified = true
                       );

                   } else {
                       //need to save transaction id
                       $order->getPayment()->setTransactionId($this->getIpnFormData('txn_id'));
                       //need to convert from order into invoice
                       $invoice = $order->prepareInvoice();
                       $invoice->register()->pay();
                       Mage::getModel('core/resource_transaction')
                           ->addObject($invoice)
                           ->addObject($invoice->getOrder())
                           ->save();
                       $order->setState(
                           Mage_Sales_Model_Order::STATE_COMPLETE, true,
                           Mage::helper('paypal')->__('Invoice #%s created', $invoice->getIncrementId()),
                           $notified = true
                       );
                   }
                } else {
                    $newOrderStatus = $this->getOrderStatus();
                    if (empty($newOrderStatus)) {
                        $newOrderStatus = $order->getStatus(); // keep order status/state;
                    }

                    $comment = $this->_prepareIpnComment();

                    $order->setState(
                        Mage_Sales_Model_Order::STATE_PROCESSING, $newOrderStatus,
                        Mage::helper('paypal')->__('Received IPN verification: %s.', $comment),
                        $notified = true
                    );
                }

                $ipnCustomerNotified = true;
                if (!$order->getPaypalIpnCustomerNotified()) {
                    $ipnCustomerNotified = false;
                    $order->setPaypalIpnCustomerNotified(1);
                }

                $order->save();

                if (!$ipnCustomerNotified) {
                    $order->sendNewOrderEmail();
                }

            }//else amount the same and there is order obj
            //there are status added to order
        }
        return $this;
    }

    /**
     * Made acction according workflow for verified == false
     *
     * @return Mage_Paypal_Model_Api_Ipn
     */
    public function processIpnFail()
    {
        $order = $this->_getOrder();
        /*
        Canceled_Reversal
        Completed
        Denied
        Expired
        Failed
        Pending
        Processed
        Refunded
        Reversed
        Voided
        */
        $comment = $this->_prepareIpnComment();
        //response error
        if (!$order->getId()) {
            /*
            * need to have logic when there is no order with the order id from paypal
            */
        } else {
            $order->addStatusToHistory(
                $order->getStatus(),//continue setting current order status
                Mage::helper('paypal')->__('PayPal IPN Invalid %s.', $comment)
            );
            $order->save();
        }
        return $this;
    }

    /**
     * Get Magento order status by corresponding paypal payment status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return '';
    }

    /**
     * Prepare ipn response comment to put it in order
     *
     * @return string
     */
    protected function _prepareIpnComment()
    {
        $paymentStatus= $this->getIpnFormData('payment_status');
        $comment = $paymentStatus;
        if ($paymentStatus == 'Pending') {
            $comment .= ' - ' . $this->getIpnFormData('pending_reason');
        } elseif ( ($paymentStatus == 'Reversed') || ($paymentStatus == 'Refunded') ) {
            $comment .= ' - ' . $this->getIpnFormData('reason_code');
        }
        return $comment;
    }
}