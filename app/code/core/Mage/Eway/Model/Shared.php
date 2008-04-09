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
 * @category   Mage
 * @package    Mage_Eway
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eway Shared Checkout Module
 */
class Mage_Eway_Model_Shared extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'eway_shared';
    
    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    
    protected $_formBlockType = 'eway/shared_form';
    protected $_allowCurrencyCode = array('AUD', 'USD', 'CAD', 'EUR', 'JPY', 'NZD', 'HKD', 'SGD', 'GBP');
    protected $_paymentMethod = 'shared';

     /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('eway/session');
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

    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('eway')->__('Selected currency code ('.$currency_code.') is not compatabile with eWAY'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('eway/' . $this->_paymentMethod . '/redirect');
    }
    
    public function getSharedFormFields()
    {
        $billing = $this->getQuote()->getBillingAddress();
        $fieldsArr = array();
        $invoiceDesc = '';
        $lengs = 0;
        foreach ($this->getQuote()->getAllItems() as $item) {
            if (strlen($invoiceDesc.$item->getProduct()->getName()) > 10000) {
                break;
            }
            $invoiceDesc .= $item->getProduct()->getName() . ', ';
        }
        $invoiceDesc = substr($invoiceDesc, 0, -2);

        $address = clone $billing;
        $address->unsFirstname();
        $address->unsLastname();
        $address->unsPostcode();
        $formatedAddress = '';
        $tmpAddress = explode(' ', str_replace("\n", ' ', trim($address->format('text'))));
        foreach ($tmpAddress as $part) {
            if (strlen($part) > 0) $formatedAddress .= $part . ' ';
        }
        
        $fieldsArr['ewayCustomerID'] = Mage::getStoreConfig('eway/' . $this->getCode() . 'api/customer_id');
        $fieldsArr['ewayTotalAmount'] = ($this->getQuote()->getGrandTotal()*100);
        $fieldsArr['ewayCustomerFirstName'] = $billing->getFirstname();
        $fieldsArr['ewayCustomerLastName'] = $billing->getLastname();
        $fieldsArr['ewayCustomerEmail'] = $this->getQuote()->getCustomerEmail();
        $fieldsArr['ewayCustomerAddress'] = trim($formatedAddress);
        $fieldsArr['ewayCustomerPostcode'] = $billing->getPostcode();
//        $fieldsArr['ewayCustomerInvoiceRef'] = '';
        $fieldsArr['ewayCustomerInvoiceDescription'] = $invoiceDesc;
        $fieldsArr['eWAYSiteTitle '] = Mage::app()->getStore()->getName();
        $fieldsArr['eWAYAutoRedirect'] = 1;
        $fieldsArr['ewayURL'] = Mage::getUrl('eway/' . $this->_paymentMethod . '/success');
        $fieldsArr['eWAYTrxnNumber'] = $this->getCheckout()->getLastRealOrderId();
        $fieldsArr['ewayOption1'] = '';
        $fieldsArr['ewayOption2'] = '';
        $fieldsArr['ewayOption3'] = '';

        $request = '';
        foreach ($fieldsArr as $k=>$v) {
            $request .= '<' . $k . '>' . $v . '</' . $k . '>';
        }
        
        if ($this->getDebug()) {
            $debug = Mage::getModel('eway/api_debug')
                ->setRequestBody($request)
                ->save();
            $this->getSession()->setLastEwayDebugId($debug->getId());
        }

        return $fieldsArr;
    }

    public function getEwaySharedUrl()
    {
         if (!$url = Mage::getStoreConfig('eway/eway_sharedapi/api_url')) {
             $url = 'https://www.eway.com.au/gateway/payment.asp';
         }
         return $url;
    }

    public function getDebug()
    {
        return Mage::getStoreConfig('eway/' . $this->getCode() . 'api/debug_flag');
    }

    public function postSubmit()
    {
        $response = $this->getFormData();
        
        if ($this->getDebug()) {
            $debug = Mage::getModel('eway/api_debug')
                ->load($this->getSession()->getLastEwayDebugId())
                ->setResponseBody(print_r($response, 1))
                ->save();
            $this->getSession()->unsLastEwayDebugId();
        }
        
        $order = Mage::getModel('sales/order');
        /** @var $order Mage_Sales_Model_Order */
        $order->loadByIncrementId($response['ewayTrxnNumber']);

        if ($response['ewayTrxnStatus'] == 'True') {

            if (!$order->canInvoice()) {
                $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('eway')->__('Error in creating an invoice.')
               );
           } else {
               $order->getPayment()->setStatus(self::STATUS_APPROVED)->setTransactionId($response['ewayTrxnReference']);
               $convertor = Mage::getModel('sales/convert_order');
               $invoice = $convertor->toInvoice($order);
               foreach ($order->getAllItems() as $orderItem) {
                   if (!$orderItem->getQtyToInvoice()) {
                       continue;
                   }
                   $item = $convertor->itemToInvoiceItem($orderItem);
                   $item->setQty($orderItem->getQtyToInvoice());
                   $invoice->addItem($item);
               }
               $invoice->collectTotals();
               $invoice->register()->capture();
               Mage::getModel('core/resource_transaction')
                   ->addObject($invoice)
                   ->addObject($invoice->getOrder())
                   ->save();
               $order->addStatusToHistory(
                    Mage::getStoreConfig('payment/' . $this->getCode() . '/order_status'));
           }
        } else {
            $order->addStatusToHistory(
                    Mage_Sales_Model_Order::STATE_CANCELED);
            $order->getPayment()->setStatus(self::STATUS_ERROR);
            $order->cancel();
        }

        $order->save();
    }

}
