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
 * @package    Mage_PackageName
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Description goes here...
 *
 * @name       Mage_Protx_Model_Form
 * @author	   Dmitriy Volik <killoff@gmail.com>
 * @date       Fri Apr 04 15:03:22 EEST 2008
 */
class Mage_Protx_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'protx_standard';
    protected $_formBlockType = 'protx/standard_form';

    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    public function getApi()
    {
        return Mage::getSingleton('protx/api_abstract');
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 15:05:49 EEST 2008
     */
    protected function getSession ()
    {
        return Mage::getSingleton('protx/session');
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 15:07:58 EEST 2008
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     *  Debug or not
     *
     *  @param    none
     *  @return	  boolean
     *  @date	  Tue Apr 08 14:57:36 EEST 2008
     */
    public function getDebug ()
    {
        return $this->getApi()->getDebug();
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
     *  Returns Target URL
     *
     *  @param    none
     *  @return	  String URL
     *  @date	  Mon Apr 07 18:54:21 EEST 2008
     */
    public function getProtxUrl ()
    {
        switch ($this->getApi()->getMode()) {
            case Mage_Protx_Model_Api_Abstract::MODE_LIVE:
                $url = 'https://ukvps.protx.com/vspgateway/service/vspform-register.vsp';
                break;
            case Mage_Protx_Model_Api_Abstract::MODE_TEST:
                $url = 'https://ukvpstest.protx.com/vspgateway/service/vspform-register.vsp';
                break;
            default: // simulator mode
                $url = 'https://ukvpstest.protx.com/VSPSimulator/VSPFormGateway.asp';
                break;
        }
        return $url;
    }

    /**
     *
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 19:35:55 EEST 2008
     */
    protected function getVendorTxCode ()
    {
        return $this->getCheckout()->getLastRealOrderId();
    }

    /**
     *  Returns cart formatted
     *  String format:
     *  Number of lines:Name1:Quantity1:CostNoTax1:Tax1:CostTax1:Total1:Name2:Quantity2:CostNoTax2...
     *
     *  @param    none
     *  @return	  string Formatted cart items
     *  @date	  Mon Apr 07 17:53:58 EEST 2008
     */
    public function getFormattedCart ()
    {
        $items = $this->getQuote()->getAllItems();
        $resultParts = array();
        $totalLines = 0;
        if ($items) {
            foreach($items as $item) {
                $cost = sprintf('%.2f', $item->getBaseCalculationPrice() - $item->getBaseDiscountAmount());
                $tax = sprintf('%.2f', $item->getBaseTaxAmount());
                $costPlusTax = sprintf('%.2f', $cost + $tax);

                $quantity = $item->getQty();
                $totalCostPlusTax = sprintf('%.2f', $quantity * $costPlusTax);

                $resultParts[] = $item->getName();
                $resultParts[] = $quantity;
                $resultParts[] = $cost;
                $resultParts[] = $tax;
                $resultParts[] = $costPlusTax;
                $resultParts[] = $totalCostPlusTax;
            }
            $totalLines = count($items);
       }

       // add delivery
       $shipping = $this->getQuote()->getShippingAddress()->getBaseShippingAmount();
       if ((int)$shipping > 0) {
           $totalLines++;
           $resultParts = array_merge($resultParts, array('Shipping','','','','',sprintf('%.2f', $shipping)));
       }

       $result = $totalLines . ':' . implode(':', $resultParts);
       return $result;
    }

    /**
     *  format Crypted string with all order data
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 19:30:40 EEST 2008
     */
    public function getCrypted ()
    {
        $shipping = $this->getQuote()->getShippingAddress();
        $billing = $this->getQuote()->getBillingAddress();

        $amount = $shipping->getBaseSubtotal()
                  - $shipping->getBaseDiscountAmount()
                  + $shipping->getBaseShippingAmount();

        $currency = $this->getQuote()->getBaseCurrencyCode();

        $queryPairs = array();

        $transactionId = $this->getVendorTxCode();
        $queryPairs['VendorTxCode'] = $transactionId;


        $queryPairs['Amount'] = sprintf('%.2f', $amount);
        $queryPairs['Currency'] = $currency;

        // Up to 100 chars of free format description
        $storeName = Mage::getStoreConfig('store/system/name');
        $queryPairs['Description'] = 'Protx Form Testing'; // . $storeName;

        $queryPairs['SuccessURL'] = Mage::getUrl('protx/standard/success');
        $queryPairs['FailureURL'] = Mage::getUrl('protx/standard/failure');

        $queryPairs['CustomerName'] = $shipping->getFirstname().' '.$shipping->getLastname();
        $queryPairs['CustomerEMail'] = $shipping->getEmail();

//        $queryPairs['VendorEMail'] = $strVendorEMail;
//        $queryPairs['eMailMessage'] = 'Thank you so very much for your order.';

        $queryPairs['BillingAddress'] = $billing->getFormated();
        $queryPairs['BillingPostCode'] = $billing->getPostcode();
    	$queryPairs['DeliveryAddress'] = $shipping->getFormated();
    	$queryPairs['DeliveryPostCode'] = $shipping->getPostcode();

        $queryPairs['Basket'] = $this->getFormattedCart();

        // For charities registered for Gift Aid
        $queryPairs['AllowGiftAid'] = '0';


        /*
            Allow fine control over AVS/CV2 checks and rules by changing this value. 0 is Default
            It can be changed dynamically, per transaction, if you wish.  See the VSP Server Protocol document
        */
        if ($this->getApi()->getPaymentType() !== Mage_Protx_Model_Api_Abstract::PAYMENT_TYPE_AUTHENTICATE) {
            $queryPairs['ApplyAVSCV2'] = '0';
        }

        /*
            Allow fine control over 3D-Secure checks and rules by changing this value. 0 is Default
            It can be changed dynamically, per transaction, if you wish.  See the VSP Server Protocol document
        */
        $queryPairs['Apply3DSecure'] = '0';

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
//                ->setTransactionId($transactionId)
                ->setRequestBody($this->getProtxUrl()."\n".print_r($queryPairs,1))
                ->save();
        }

        // Encrypt the plaintext string for inclusion in the hidden field
        $result = $this->Array2Crypted($queryPairs);
        return $result;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 15:07:58 EEST 2008
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('protx/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());
        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('protx/info_standard', $name);
        $block->setPayment($this->getPayment());
        return $block;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('protx/standard/redirect');
    }

    /**
     *  Description goes here...
     *
     *  @param    Mage_Sales_Model_Order_Payment $payment
     *  @return	  void
     *  @date	  Mon Apr 07 13:33:39 EEST 2008
     */
    protected function onOrderValidate (Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }

    /**
     *  The SimpleXor encryption algorithm
     *
     *  @param    string String to be crypted
     *  @return	  string Crypted string
     *  @date	  Mon Apr 07 15:24:40 EEST 2008
     */
    public function simpleXOR ($string)
    {
        $result = '';
        $cryptKey = $this->getApi()->getCryptKey();

        // Initialise key array
        $keyList = array();

        // Convert $cryptKey into array of ASCII values
        for($i = 0; $i < strlen($cryptKey); $i++){
            $keyList[$i] = ord(substr($cryptKey, $i, 1));
        }

        // Step through string a character at a time
        for($i = 0; $i < strlen($string); $i++) {
            // Get ASCII code from string, get ASCII code from key (loop through with MOD),
            // XOR the two, get the character from the result
            // % is MOD (modulus), ^ is XOR
            $result .= chr(ord(substr($string, $i, 1)) ^ ($keyList[$i % strlen($cryptKey)]));
        }
        return $result;
    }

    /**
     *  Extract possible response values into array from query string
     *
     *  @param    string Query string i.e. var1=value1&var2=value3...
     *  @return	  array
     *  @date	  Mon Apr 07 15:24:40 EEST 2008
     */
    public function getToken($queryString) {

        // List the possible tokens
        $Tokens = array(
                        "Status",
                        "StatusDetail",
                        "VendorTxCode",
                        "VPSTxId",
                        "TxAuthNo",
                        "Amount",
                        "AVSCV2",
                        "AddressResult",
                        "PostCodeResult",
                        "CV2Result",
                        "GiftAid",
                        "3DSecureStatus",
                        "CAVV"
                        );

        // Initialise arrays
        $output = array();
        $resultArray = array();

        // Get the next token in the sequence
        $c = count($Tokens);
        for ($i = $c - 1; $i >= 0 ; $i--){
            // Find the position in the string
            $start = strpos($queryString, $Tokens[$i]);
            // If it's present
            if ($start !== false){
                // Record position and token name
                $resultArray[$i]['start'] = $start;
                $resultArray[$i]['token'] = $Tokens[$i];
            }
        }

        // Sort in order of position
        sort($resultArray);

        // Go through the result array, getting the token values
        $c = count($resultArray);
        for ($i = 0; $i < $c; $i++){
            // Get the start point of the value
            $valueStart = $resultArray[$i]['start'] + strlen($resultArray[$i]['token']) + 1;
            // Get the length of the value
            if ($i == $c-1) {
                $output[$resultArray[$i]['token']] = substr($queryString, $valueStart);
            } else {
                $valueLength = $resultArray[$i+1]['start'] - $resultArray[$i]['start'] - strlen($resultArray[$i]['token']) - 2;
                $output[$resultArray[$i]['token']] = substr($queryString, $valueStart, $valueLength);
            }

        }

        return $output;
    }

    /**
     *  Convert array (key => value, key => value, ...) to crypted string
     *
     *  @param    $array Array to be converted
     *  @return	  Crypted string
     *  @date	  Mon Apr 07 16:34:22 EEST 2008
     */
    public function Array2Crypted ($array)
    {
        $parts = array();
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $parts[] = $k . '=' . $v;
            }
        }
        $result = implode('&', $parts);
        $result = $this->simpleXOR($result);
        $result = $this->getApi()->base64Encode($result);
        return $result;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     *  @date	  Mon Apr 07 16:54:26 EEST 2008
     */
    public function Cryptred2Array ($crypted)
    {
        $decoded = $this->getApi()->base64Decode($crypted);
        $uncrypted = $this->simpleXOR($decoded);
        $tokens = $this->getToken($uncrypted);
        return $tokens;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     *  @date	  Mon Apr 07 14:24:13 EEST 2008
     */
    public function getStandardCheckoutFormFields ()
    {

        $fields = array(
                        'VPSProtocol'       => $this->getApi()->getVersion(),
                        'TxType'            => $this->getApi()->getPaymentType(),
                        'Vendor'            => $this->getApi()->getVendorName(),
                        'Crypt'             => $this->getCrypted()
                        );
        return $fields;
    }

    /**
     *  Crypted contains:
     *
        [Status] => OK
        [StatusDetail] => Successfully Authorised Transaction
        [VendorTxCode] => magento77771148
        [VPSTxId] => {CA8D1CC1-22E8-4F42-8FDD-BAF0F1A85C8B}
        [TxAuthNo] => 7349
        [Amount] => 463
        [AVSCV2] => ALL MATCH
        [AddressResult] => MATCHED
        [PostCodeResult] => MATCHED
        [CV2Result] => MATCHED
        [GiftAid] => 0
        [3DSecureStatus] => OK
        [CAVV] => MNAXJRSRZK22PYKXPCFG1Z
     */

    /**
     *  Failure response from Protx
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 21:43:22 EEST 2008
     */
    public function onFailureResponse ()
    {
        $response = $this->Cryptred2Array($this->getResponseData('crypt'));
        $transactionId = $response['VendorTxCode'];

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setResponseBody(print_r($response,1))
//                ->setId($transactionId)
                ->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($transactionId);

        if (!$order->getId()) {
            /*
            * need to have logic when there is no order with the order id from paypal
            */
            return false;
        }
        $order->addStatusToHistory(
            'canceled',
            Mage::helper('protx')->__('Order '.$order->getId().' was canceled by customer')
        );

        if ($response['Status'] == 'ABORT') {
            // CANCEL button
        }

        $order->save();
    }

    /**
     *  Success response from Protx
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 15:06:36 EEST 2008
     */
    public function onSuccessResponse ()
    {
        $response = $this->Cryptred2Array($this->getResponseData('crypt'));
        $transactionId = $response['VendorTxCode'];

        if ($this->getDebug()) {
            Mage::getModel('protx/api_debug')
                ->setResponseBody(print_r($response,1))
//                ->setId($transactionId)
                ->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($transactionId);

        if (!$order->getId()) {
            /*
            * need to have logic when there is no order with the order id from paypal
            */
            return false;
        }

        if (sprintf('%.2f', $response['Amount']) != sprintf('%.2f', $order->getGrandTotal())) {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('paypal')->__('Order total amount does not match paypal gross total amount')
            );
        } else {
            $order->getPayment()->setTransactionId($response['VPSTxId']);
            if ($this->getApi()->getPaymentType() == Mage_Protx_Model_Api_Abstract::PAYMENT_TYPE_PAYMENT) {
                $this->saveInvoice($order);
            } else {
                $order->addStatusToHistory(
                    $this->getApi()->getNewOrderStatus(), //update order status to processing after creating an invoice
                    Mage::helper('protx')->__('Order '.$invoice->getIncrementId().' has pending status')
                );
            }
        }
        $order->save();
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean
     *  @date	  Tue Apr 08 20:26:14 EEST 2008
     */
    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
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
                'processing',//update order status to processing after creating an invoice
                Mage::helper('protx')->__('Invoice '.$invoice->getIncrementId().' was created')
            );

            return true;

        } else {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('protx')->__('Error in creating an invoice')
            );

            return false;
        }
    }

}