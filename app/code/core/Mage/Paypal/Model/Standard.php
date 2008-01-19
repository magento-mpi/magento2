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
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Standard Checkout Module
 *
 * @author     Lindy Kyaw <lindy@varien.com>
 */
class Mage_Paypal_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'paypal_standard';
    protected $_formBlockType = 'paypal/standard_form';


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
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/standard_form', $name)
            ->setMethod('paypal_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/standard/form.phtml');

        return $block;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('paypal/standard/redirect');
    }

    public function getStandardCheckoutFormFields()
    {
        $a = $this->getQuote()->getShippingAddress();
        $amount=$a->getSubtotal()-$a->getDiscountAmount();

        $businessName = Mage::getStoreConfig('paypal/wps/business_name');
        $storeName = Mage::getStoreConfig('store/system/name');

        $sArr = array(
            'business'          => Mage::getStoreConfig('paypal/wps/business_account'),
            'return'            => Mage::getUrl('paypal/standard/success'),
            'cancel_return'     => Mage::getUrl('paypal/standard/cancel'),
            'notify_url'        => Mage::getUrl('paypal/standard/ipn'),
            'item_name'         => $businessName ? $businessName : $storeName,
            'amount'            => sprintf('%.2f', $amount),
            'cmd'               => '_xclick',
            'invoice'           => $this->getCheckout()->getLastRealOrderId(),
            'mc_currency'       => $this->getQuote()->getStoreCurrencyCode(),
            'address_override'  => 1,
            'first_name'        => $a->getFirstname(),
            'last_name'         => $a->getLastname(),
            'address1'          => $a->getStreet(1),
            'address2'          => $a->getStreet(2),
            'city'              => $a->getCity(),
            'state'             => $a->getRegionCode(),
            'country'           => $a->getCountry(),
            'zip'               => $a->getPostcode(),
        );


        /*
        $items=$this->getQuote()->getAllItems();
        if($items){
            $i=1;
            foreach($items as $item){
                echo "<pre>"; print_r($item->getData()); echo"</pre>";
                 $sArr = array_merge($sArr, array(
                    'item_name_'.$i      => $item->getName(),
                    'quantity_'.$i      => $item->getQty(),

                ));
                $i++;
            }
        }
        */

        $shipping = sprintf('%.2f', $this->getQuote()->getShippingAddress()->getShippingAmount());
        if($shipping>0){
             $sArr = array_merge($sArr, array(
                    'shipping' => $shipping
                    )
             );
        }
        $tax = sprintf('%.2f', $this->getQuote()->getShippingAddress()->getTaxAmount());
        if($tax>0) {
             $sArr = array_merge($sArr, array(
                    'tax' => $tax
                    )
             );
        }
        return $sArr;
    }

    public function getPaypalUrl()
    {
         if (Mage::getStoreConfig('paypal/wps/sandbox_flag')==1) {
             $url='https://www.sandbox.paypal.com/cgi-bin/webscr';
         } else {
             $url='https://www.paypal.com/cgi-bin/webscr';
         }
         return $url;
    }


    public function ipnPostSubmit()
    {
        $sReq = '';
        foreach($this->getIpnFormData() as $k=>$v) {
            $sReq .= '&'.$k.'='.urlencode(stripslashes($v));
        }
        //append ipn commdn
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);
        $debugFlag = Mage::getStoreConfig('paypal/wps/debug_flag');
        if ($debugFlag) {
            $debug = Mage::getModel('paypal/api_debug')
                    ->setApiEndpoint($this->getPaypalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }
        $http = new Varien_Http_Adapter_Curl();
        $http->write(Zend_Http_Client::POST,$this->getPaypalUrl(), '1.1', array(), $sReq);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        if ($debugFlag) {
            $debug->setResponseBody($response)->save();
        }

         //when verified need to convert order into invoice
        $id=$this->getIpnFormData('invoice');
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($id);

        if ($response=='VERIFIED') {
            if (!$order->getId()) {
                /*
                * need to have logic when there is no order with the order id from paypal
                */

            } else {
                if ($this->getIpnFormData('mc_gross')!=$order->getGrandTotal()) {
                    //when grand total does not equal, need to have some logic to take care
                    $order->addStatusToHistory(
                        $order->getStatus(),//continue setting current order status
                        Mage::helper('paypal')->__('Order total amount does not match paypal gross total amount')
                    );

                } else {
                   if (!$order->canInvoice()) {
                       //when order cannot create invoice, need to have some logic to take care
                       $order->addStatusToHistory(
                            $order->getStatus(),//continue setting current order status
                            Mage::helper('paypal')->__('Order cannot create invoice')
                       );

                   } else {
                       //need to save transaction id
                       $order->getPayment()->setTransactionId($this->getIpnFormData('txn_id'));
                       //need to convert from order into invoice
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
                            $order->getStatus(),//continue setting current order status
                            Mage::helper('paypal')->__('Invoice '.$invoice->getIncrementId().' was created')
                       );
                   }
                }//else amount the same and there is order obj
                //there are status added to order
                $order->save();
            }
        }else{
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
            $payment_status= $this->getIpnFormData('payment_status');
            $comment = $payment_status;
            if ($payment_status == 'Pending') {
                $comment .= ' - ' . $this->getIpnFormData('pending_reason');
            } elseif ( ($payment_status == 'Reversed') || ($payment_status == 'Refunded') ) {
                $comment .= ' - ' . $this->getIpnFormData('reason_code');
            }
            //response error
            if (!$order->getId()) {
                /*
                * need to have logic when there is no order with the order id from paypal
                */
            } else {
                $order->addStatusToHistory(
                    $order->getStatus(),//continue setting current order status
                    Mage::helper('paypal')->__('Paypal IPN Invalid.'.$comment)
                );
                $order->save();
            }
        }
    }

}
