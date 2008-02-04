<?php

class Mage_GoogleCheckout_Model_Api_Xml_Callback extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    public function process()
    {
        // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
        $xmlResponse = isset($GLOBALS['HTTP_RAW_POST_DATA']) ?
            $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (get_magic_quotes_gpc()) {
            $xmlResponse = stripslashes($xmlResponse);
        }
$this->log("REQUEST: ".$xmlResponse);
        $debug = Mage::getModel('googlecheckout/api_debug')->setDir('in')
            ->setUrl('process')
            ->setRequestBody($xmlResponse)
            ->save();

        list($root, $data) = $this->getGResponse()->GetParsedXML($xmlResponse);

        $debug->setUrl($root)->save();

        $this->getGResponse()->SetMerchantAuthentication($this->getMerchantId(), $this->getMerchantKey());
        $status = $this->getGResponse()->HttpAuthentication();

        if (!$status || empty($data[$root])) {
            exit;
        }

        $this->setRootName($root)->setRoot($data[$root]);

        $this->getGResponse()->setSerialNumber($this->getData('root/serial-number'));

        $method = '_response'.uc_words($root, '', '-');
        if (method_exists($this, $method)) {
            ob_start();

            try {
                $this->$method();
            } catch (Exception $e) {
                $this->log('ERROR: '.$e->getMessage());
            }

            $response = ob_get_flush();
$this->log("RESPONSE: ".$response);
            $debug->setResponseBody($response)->save();
        } else {
            $this->getGResponse()->SendBadRequestStatus("Invalid or not supported Message");
        }

        return $this;
    }

    protected function _getApiUrl()
    {
        return null;
    }

    protected function getGoogleOrderNumber()
    {
        return $this->getData('root/google-order-number/VALUE');
    }

    protected function _responseRequestReceived()
    {

    }

    protected function _responseError()
    {

    }

    protected function _responseDiagnosis()
    {

    }

    protected function _responseCheckoutRedirect()
    {

    }

    protected function _responseMerchantCalculationCallback()
    {
        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $address = $quote->getShippingAddress();

        $googleAddress = $this->getData('root/calculate/addresses/anonymous-address');
        $address->setCountryId($googleAddress['country-code']['VALUE'])
            ->setRegion($googleAddress['region']['VALUE'])
            ->setCity($googleAddress['city']['VALUE'])
            ->setPostcode($googleAddress['postal-code']['VALUE']);

        $carriers = array();
        $gRequestMethods = $this->getData('root/calculate/shipping/method');
        foreach (Mage::getStoreConfig('carriers') as $carrierCode=>$carrierConfig) {
            foreach ($gRequestMethods as $method) {
                $title = (string)$carrierConfig->title;
                if ($title && strpos($method['name'], $title)===0) {
                    $carriers[$carrierCode] = $title;
                }
            }
        }

        $result = Mage::getModel('shipping/shipping')
            ->collectRatesByAddress($address, array_keys($carriers))
            ->getResult();

        $errors = array();
        $rates = array();
        foreach ($result->getAllRates() as $rate) {
            if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
                $errors[$rate->getCarrierTitle()] = 1;
            } else {
                $rates[$rate->getCarrierTitle().' - '.$rate->getMethodTitle()] = $rate->getPrice();
            }
        }
        $merchantCalculations = new GoogleMerchantCalculations($this->getCurrency());

        $addressId = $googleAddress['id'];
        foreach ($gRequestMethods as $method) {
            $methodName = $method['name'];
            $result = new GoogleResult($addressId);
            if (!empty($errors)) {
                $continue = false;
                foreach ($errors as $carrier=>$dummy) {
                    if (strpos($methodName, $carrier)===0) {
                        $result->SetShippingDetails($methodName, 0, "false");
                        $merchantCalculations->AddResult($result);
                        $continue = true;
                        break;
                    }
                }
                if ($continue) {
                    continue;
                }
            }
            if (!empty($rates[$methodName])) {
                $result->SetShippingDetails($methodName, $rates[$methodName], "true");
                $merchantCalculations->AddResult($result);
            }
        }

        $this->getGResponse()->ProcessMerchantCalculations($merchantCalculations);
    }

    protected function _responseNewOrderNotification()
    {
        $this->getGResponse()->SendAck();

        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $convertQuote = Mage::getModel('sales/convert_quote');

        $order = $convertQuote->toOrder($quote);
        $order->setExtOrderId($this->getGoogleOrderNumber());
        $order->setExtCustomerId($this->getData('root/buyer-id/VALUE'));

        $billing = $this->_importGoogleAddress($this->getData('root/buyer-billing-address'));
        $order->setBillingAddress($billing);

        $shipping = $this->_importGoogleAddress($this->getData('root/buyer-shipping-address'));
        $order->setShippingAddress($shipping);

        if (!$order->getCustomerEmail()) {
            $order->setCustomerEmail($billing->getEmail())
                ->setCustomerFirstname($billing->getFirstname())
                ->setCustomerLastname($billing->getLastname());
        }

        foreach ($quote->getAllItems() as $item) {
            $order->addItem($convertQuote->itemToOrderItem($item));
        }

        $this->_importGoogleTotals($order);

        $order->setSubtotal($quote->getShippingAddress()->getSubtotal())
            ->setDiscountAmount($quote->getShippingAddress()->getDiscountAmount());

        $payment = Mage::getModel('sales/order_payment')
            ->setMethod('googlecheckout')
            ->setAdditionalData(
                $this->__('Google Order Number: %s', $this->getGoogleOrderNumber())."\n".
                $this->__('Google Buyer Id: %s', $this->getData('root/buyer-id/VALUE'))
            )
            ->setAmountOrdered($order->getGrandTotal())
            ->setShippingAmount($order->getShippingAmount());
        $order->setPayment($payment);

        $order->place();
        $order->save();

        $order->sendNewOrderEmail();

        Mage::getSingleton('checkout/session')
            ->setLastQuoteId($quote->getId())
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());

        if ($this->getData('root/buyer-marketing-preferences/email-allowed/VALUE')==='true') {
            //TODO:sign up
        }

        $this->getGRequest()->SendMerchantOrderNumber($order->getExtOrderId(), $order->getIncrementId());
    }

    protected function _importGoogleAddress($gAddress, Varien_Object $oAddress=null)
    {
        if (is_array($gAddress)) {
            $gAddress = new Varien_Object($gAddress);
        }

        if (!$oAddress) {
            $oAddress = Mage::getModel('sales/order_address');
        }

        if ($nameArr = $gAddress->getData('structured-name')) {
            $oAddress
                ->setFirstname($nameArr['first-name']['VALUE'])
                ->setLastname($nameArr['last-name']['VALUE']);
        } else {
            $nameArr = explode(' ', $gAddress->getData('contact-name/VALUE'), 2);
            $oAddress->setFirstname($nameArr[0]);
            if (!empty($nameArr[1])) {
                $oAddress->setLastname($nameArr[1]);
            }
        }
        $oAddress
            ->setCompany($gAddress->getData('company-name/VALUE'))
            ->setEmail($gAddress->getData('email/VALUE'))
            ->setStreet(trim($gAddress->getData('address1/VALUE')."\n".$gAddress->getData('address2/VALUE')))
            ->setCity($gAddress->getData('city/VALUE'))
            ->setRegion($gAddress->getData('region/VALUE'))
            ->setPostcode($gAddress->getData('postal-code/VALUE'))
            ->setCountryId($gAddress->getData('country-code/VALUE'))
            ->setTelephone($gAddress->getData('phone/VALUE'))
            ->setFax($gAddress->getData('fax/VALUE'));

        return $oAddress;
    }

    protected function _importGoogleTotals($order)
    {
        $order->setTaxAmount($this->getData('root/order-adjustment/total-tax/VALUE'));

        $prefix = 'root/order-adjustment/shipping/';
        if ($shipping = $this->getData($prefix.'carrier-calculated-shipping-adjustment')) {
            $method = 'googlecheckout_carrier';
        } elseif ($shipping = $this->getData($prefix.'merchant-calculated-shipping-adjustment')) {
            $method = 'googlecheckout_merchant';
        } elseif ($shipping = $this->getData($prefix.'flat-rate-shipping-adjustment')) {
            $method = 'googlecheckout_flatrate';
        } elseif ($shipping = $this->getData($prefix.'pickup-shipping-adjustment')) {
            $method = 'googlecheckout_pickup';
        }
        if (!empty($method)) {
            $order->setShippingMethod($method)
                ->setShippingDescription($shipping['shipping-name']['VALUE'])
                ->setShippingAmount($shipping['shipping-cost']['VALUE']);
        }

        $order->setGrandTotal($this->getData('root/order-total/VALUE'));
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->hasData('$orderorder')) {
            $order = Mage::getModel('sales/order')
                ->loadByAttribute('ext_order_id', $this->getGoogleOrderNumber());
            if (!$order->getId()) {
                Mage::throwException('Invalid Order: '.$this->getGoogleOrderNumber());
            }
            $this->setData('order', $order);
        }
        return $this->getData('order');
    }

    protected function _responseRiskInformationNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $order
            ->setRemoteIp($this->getData('root/risk-information/ip-address/VALUE'));

        $payment
            ->setCcLast4($this->getData('root/risk-information/partial-cc-number/VALUE'))
            ->setCcAvsStatus($this->getData('root/risk-information/avs-response/VALUE'))
            ->setCcCidStatus($this->getData('root/risk-information/cvn-response/VALUE'));

        $msg = $this->__('Google Risk Information:');
        $msg .= '<br />'.$this->__('IP Address: %s', '<strong>'.$order->getRemoteIp().'</strong>');
        $msg .= '<br />'.$this->__('CC Partial: xxxx-%s', '<strong>'.$payment->getCcLast4().'</strong>');
        $msg .= '<br />'.$this->__('AVS Status: %s', '<strong>'.$payment->getCcAvsStatus().'</strong>');
        $msg .= '<br />'.$this->__('CID Status: %s', '<strong>'.$payment->getCcCidStatus().'</strong>');
        $msg .= '<br />'.$this->__('Buyer account age: %s', '<strong>'.$this->getData('root/risk-information/buyer-account-age/VALUE').'</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }

    protected function _responseAuthorizationAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $payment->setAmountAuthorized($this->getData('root/authorization-amount/VALUE'));

        $expDate = $this->getData('root/authorization-expiration-date/VALUE');
        $expDate = new Zend_Date($expDate);
        $msg = $this->__('Google Authorization:');
        $msg .= '<br />'.$this->__('Amount: %s', '<strong>'.Mage::helper('core')->currency($payment->getAmountAuthorized()).'</strong>');
        $msg .= '<br />'.$this->__('Expiration: %s', '<strong>'.$expDate->toString().'</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }

    protected function _responseChargeAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $totalCharged = $this->getData('root/total-charge-amount/VALUE');
        $payment->setAmountCharged($totalCharged);

        $msg = $this->__('Google Charge:');
        $msg .= '<br />'.$this->__('Latest Charge: %s', '<strong>'.Mage::helper('core')->currency($this->getData('root/latest-charge-amount/VALUE')).'</strong>');
        $msg .= '<br />'.$this->__('Total Charged: %s', '<strong>'.Mage::helper('core')->currency($totalCharged).'</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();

        #$this->getGRequest()->SendDeliverOrder($this->getGoogleOrderNumber(), 'UPS', '1Z239452934523455', 'true');

        #$this->getGRequest()->SendArchiveOrder($this->getGoogleOrderNumber());
    }

    protected function _responseChargebackAmountNotification()
    {
        $this->getGResponse()->SendAck();
    }

    protected function _responseRefundAmountNotification()
    {
        $this->getGResponse()->SendAck();
    }

    protected function _responseOrderStateChangeNotification()
    {
        $this->getGResponse()->SendAck();

        $prevFinancial = $this->getData('root/previous-financial-order-state/VALUE');
        $newFinancial = $this->getData('root/new-financial-order-state/VALUE');
        $prevFulfillment = $this->getData('root/previous-fulfillment-order-state/VALUE');
        $newFulfillment = $this->getData('root/new-fulfillment-order-state/VALUE');

        $msg = $this->__('Google order status change:');
        if ($prevFinancial!=$newFinancial) {
            $msg .= "<br />".$this->__('Financial: %s -> %s', '<strong>'.$prevFinancial.'</strong>', '<strong>'.$newFinancial.'</strong>');
        }
        if ($prevFulfillment!=$newFulfillment) {
            $msg .= "<br />".$this->__('Fulfillment: %s -> %s', '<strong>'.$prevFulfillment.'</strong>', '<strong>'.$newFulfillment.'</strong>');
        }
        $this->getOrder()
            ->addStatusToHistory($this->getOrder()->getStatus(), $msg)
            ->save();

        $method = '_orderStateChangeFinancial'.uc_words(strtolower($newFinancial), '', '_');
        if (method_exists($this, $method)) {
            $this->$method();
        }

        $method = '_orderStateChangeFulfillment'.uc_words(strtolower($newFulfillment), '', '_');
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function _orderStateChangeFinancialReviewing()
    {

    }

    protected function _orderStateChangeFinancialChargeable()
    {
        #$this->getGRequest()->SendProcessOrder($this->getGoogleOrderNumber());
        #$this->getGRequest()->SendChargeOrder($this->getGoogleOrderNumber(), '');
    }

    protected function _orderStateChangeFinancialCharging()
    {

    }

    protected function _orderStateChangeFinancialCharged()
    {

    }

    protected function _orderStateChangeFinancialPaymentDeclined()
    {

    }

    protected function _orderStateChangeFinancialCancelled()
    {

    }

    protected function _orderStateChangeFinancialCancelledByGoogle()
    {
        $this->getGRequest()->SendBuyerMessage($this->getGoogleOrderNumber(), "Sorry, your order is cancelled by Google", true);
    }

    protected function _orderStateChangeFulfillmentNew()
    {

    }

    protected function _orderStateChangeFulfillmentProcessing()
    {

    }

    protected function _orderStateChangeFulfillmentDelivered()
    {

    }

    protected function _orderStateChangeFulfillmentWillNotDeliver()
    {

    }

}