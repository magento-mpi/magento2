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
$this->log($xmlResponse);
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

            $this->$method();

            $response = ob_get_flush();
$this->log($response);
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

    /**
     * Commands to send the various order processing APIs
     * Send charge order : $Grequest->SendChargeOrder($this->getGoogleOrderNumber(), <amount>);
     * Send process order : $Grequest->SendProcessOrder($this->getGoogleOrderNumber());
     * Send deliver order: $Grequest->SendDeliverOrder($this->getGoogleOrderNumber(), <carrier>, <tracking-number>, <send_mail>);
     * Send archive order: $Grequest->SendArchiveOrder($this->getGoogleOrderNumber());
     *
     */

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
        $addressId = $this->getData('root/calculate/addresses/anonymous-address/id');
#echo "TEST:"; print_r($addressId); return;
        foreach (array('DHL - Express'=>6) as $method=>$amount) {
            $result = new GoogleResult($addressId);
            $result->SetShippingDetails($method, $amount, "true");
        }

        $merchantCalculations->AddResult($result);

        $this->getGResponse()->ProcessMerchantCalculations($merchantCalculations);
        /*

        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');

        $quote = Mage::getModel('sales/quote')->load($quoteId);
echo $quote->getId(); return;
        $address = $quote->getShippingAddress();
print_r($address->getData()); return;

        $googleAddress = $this->getData('root/calculate/addresses/anonymous-address');
        $address->setCountryId($googleAddress['country-code']['VALUE'])
            ->setRegionId($googleAddress['region']['VALUE'])
            ->setCity($googleAddress['city']['VALUE'])
            ->setPostcode($googleAddress['postal-code']['VALUE']);

        $carriers = array();
        foreach ($this->getData('root/calculate/shiping') as $method) {
            list($carrierCode, $methodCode) = explode('/', $method['name']);
            $carriers[$carrierCode][$methodCode] = 1;
        }

        $result = Mage::getModel('shipping/shipping')
            ->collectRates($address, array_keys($carriers))
            ->getResult();
#echo "<pre>".print_r($result,1)."</pre>"; return;

        $merchantCalculations = new GoogleMerchantCalculations($this->getCurrency());

        $addressId = $googleAddress['id'];
        foreach ($carriers as $carrierCode=>$methods) {
            foreach ($methods as $methodCode=>$method) {
                $result = new GoogleResult($addressId);
                $result->SetShippingDetails($method, $amount, "true");
            }
        }

        $merchantCalculations->AddResult($result);

        $this->getGResponse()->ProcessMerchantCalculations($merchantCalculations);
*/
    }

    protected function _responseNewOrderNotification()
    {
        $hlp = Mage::helper('googlecheckout');

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

        $payment = Mage::getModel('sales/order_payment')
            ->setMethod('googlecheckout')
            ->setAdditionalData(
                $hlp->__('Google Order Number: %s', $this->getGoogleOrderNumber())."\n".
                $hlp->__('Google Buyer Id: %s', $this->getData('root/buyer-id/VALUE'))
            );
        $order->setPayment($payment);

        $this->_importGoogleTotals($order);

        $order->save();

        $order->sendNewOrderEmail();

        Mage::getSingleton('checkout/session')
            ->setLastQuoteId($quote->getId())
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());

        if ($this->getData('root/buyer-marketing-preferences/email-allowed/VALUE')==='true') {
            //sign up
        }

        $this->getGResponse()->SendAck();
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

    protected function _responseOrderStateChangeNotification()
    {
        $financial = $this->getData('root/new-financial-order-state/VALUE');
        $fulfillment = $this->getData('root/new-fulfillment-order-state/VALUE');

        switch ($financial) {
            case 'REVIEWING':
                break;

            case 'CHARGEABLE':
                #$this->getGRequest()->SendProcessOrder($this->getGoogleOrderNumber());
                #$this->getGRequest()->SendChargeOrder($this->getGoogleOrderNumber(), '');
                break;

            case 'CHARGING':
                break;

            case 'CHARGED':
                break;

            case 'PAYMENT_DECLINED':
                break;

            case 'CANCELLED':
                break;

            case 'CANCELLED_BY_GOOGLE':
                $this->getGRequest()->SendBuyerMessage($this->getGoogleOrderNumber(), "Sorry, your order is cancelled by Google", true);
                break;

            default:
                break;
       }

        switch ($fulfillment) {
            case 'NEW':
                break;

            case 'PROCESSING':
                break;

            case 'DELIVERED':
                break;

            case 'WILL_NOT_DELIVER':
                break;

            default:
                break;
        }

        $this->getGResponse()->SendAck();
    }

    protected function _responseAuthorizationAmountNotification()
    {
        $this->getGResponse()->SendAck();
    }

    protected function _responseChargeAmountNotification()
    {
        $this->getGRequest()->SendDeliverOrder($this->getGoogleOrderNumber(), 'UPS', '1Z239452934523455', 'true');

        $this->getGRequest()->SendArchiveOrder($this->getGoogleOrderNumber());

        $this->getGResponse()->SendAck();
    }

    protected function _responseChargebackAmountNotification()
    {
        $this->getGResponse()->SendAck();
    }

    protected function _responseRefundAmountNotification()
    {
        $this->getGResponse()->SendAck();
    }

    protected function _responseRiskInformationNotification()
    {
        $this->getGResponse()->SendAck();
    }
}