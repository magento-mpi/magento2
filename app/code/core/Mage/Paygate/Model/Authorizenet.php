<?php

class Mage_Paygate_Model_Authorizenet extends Mage_Sales_Model_Payment_Abstract
{
    const REQUEST_METHOD_CC = 'CC';
    const REQUEST_METHOD_ECHECK = 'ECHECK';

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const REQUEST_TYPE_CREDIT = 'CREDIT';
    const REQUEST_TYPE_VOID = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';
    
    const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
    const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ECHECK_ACCT_TYPE_SAVINGS = 'SAVINGS';
    
    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    const RESPONSE_DELIM_CHAR = ',';

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment_cc_form', $name)
            ->assign('method', 'authorizenet')
            ->init($this->_payment);
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment_cc_info', $name)
            ->init($this->_payment);
        return $block;
    }
    
    public function onOrderCreate(Mage_Sales_Model_Order $order)
    {
        #$request = $this->buildRequest($order->)
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice $invoice)
    {
        
    }
    
    /**
     * Enter description here...
     *
     * @link http://www.authorize.net/support/AIM_guide.pdf
     * @param Mage_Sales_Model_Document $document
     * @return unknown
     */
    public function buildRequest(Varien_Data_Object $payment, Varien_Data_Object $document=null)
    {
        $config = Mage::getSingleton('sales', 'config')->getPaymentConfig('authorizenet');
        
        $request = Mage::getModel('paygate', 'authorizenet_request')
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXDelimChar(self::RESPONSE_DELIM_CHAR)
            ->setXRelayResponse('False');
        
        $request->setXTestRequest($config->is('test') ? 'TRUE' : 'FALSE');
            
        $request->setXLogin($config->login)
            ->setXTranKey($config->transKey)
            ->setXAmount($payment->getAmount())
            ->setXType($payment->getAnetTransType())
            ->setXMethod($payment->getAnetTransMethod());
            
        switch ($request->getAnetTransType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $request->setXTransId($payment->getAnetTransId());
                break;
                
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setXAuthCode($payment->getAnetAuthCode());
                break;
        }
            
        if (!empty($document)) {
            
            $billing = $document->getBillingAddress();
            if (!empty($billing)) {
                $request->setXFirstName($billing->getFirstName())
                    ->setXLastName($bililng->getLastName())
                    ->setXCompany($billing->getCompany())
                    ->setXAddress($billing->getStreet())
                    ->setXCity($bililng->getCity())
                    ->setXState($billing->getRegionName())
                    ->setXZip($bililng->getPostcode())
                    ->setXCountry($biling->getCountryName())
                    ->setXPhone($billing->getPhone())
                    ->setXFax($billing->getFax())
                    ->setXCustId($billing->getCustomerId())
                    ->setXCustomerIp($billing->getRemoteIp())
                    ->setXCustomerTaxId($billing->getTaxId())
                    ->setXEmail($bililng->getEmail())
                    ->setXEmailCustomer($config->is('emailCustomer'))
                    ->setXMerchantEmail($config->is('emailMerchant'))
                    ->setXInvoiceNum($b);
            }
            
            $shipping = $document->getShippingAddress();
            if (!empty($shipping)) {
                $request->setXShipToFirstName($shipping->getFirstName())
                    ->setXShipToLastName($shipping->getLastName())
                    ->setXShipToCompany($shipping->getCompany())
                    ->setXShipToAddress($shipping->getStreet())
                    ->setXShipToCity($shipping->getCity())
                    ->setXShipToState($shipping->getRegionName())
                    ->setXShipToZip($shipping->getPostcode())
                    ->setXShipToCountry($shipping->getCountryName());
            }
            
            /** TODO: itemized order information
            $items = $document->getEntitiesByType('item');
            foreach ($items as $item) {
                
            }
            */
            
            $request->setXPoNum($document->getPoNumber())
                ->setXTax($document->getTaxAmount())
                ->setXFreight($document->getShippingAmount);
        }
        
        switch ($payment->getAnetRequestType()) {
            case self::REQUEST_METHOD_CC:
                $request->setXCardNum($payment->getCcNumber())
                    ->setXExpDate($payment->getCcExpires())
                    ->setXCardCode($payment->getCcCvv2());
                break;
                
            case self::REQUEST_METHOD_ECHECK:
                $request->setXBankAbaCode($payment->getEcheckRoutingNumber())
                    ->setXBankName($payment->getEcheckBankName())
                    ->setXBankAcctNum($payment->getEcheckAccountNumber())
                    ->setXBankAcctType($payment->getEcheckAccountType())
                    ->setXBankAcctName($payment->getEcheckAccountName())
                    ->setXEcheckType($payment->getEcheckType());
                break;
        }

        return $request;
    }
    
    public function postRequest(Varien_Data_Object $request)
    {
        $cgi = Mage::getSingleton('sales', 'config')->getPaymentDefaults($r['vendor'])->cgi;
        $client = new Zend_Http_Client();
        $uri = ((string)$cgi->protocol).'://'.((string)$cgi->host).':'.((string)$cgi->port).((string)$cgi->url);
        $client->setUri($uri);
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterGet();
        $response = $client->request($request->getData());
        $responseBody = $response->getBody();
        
        $requestArr = array();
        foreach ($request->getData() as $key=>$value) {
            $requestArr[] = urlencode($key).'='.urlencode($value);
        }
        $requestBody = join('&', $requestArr);        
        $r = explode(self::REQUEST_DELIM_CHAR, $responseBody);

        $result = Mage::getModel('paygate', 'authorizenet_result');
        $result->setRawRequest($requestBody);
        $result->setRawResponse($responseBody);
        
        $result->setResponseCode($r[0])
            ->setResponseSubcode($r[1])
            ->setResponseReasonCode($r[2])
            ->setResponseReasonText($r[3])
            ->setApprovalCode($r[4])
            ->setAvsResultCode($r[5])
            ->setTransactionId($r[6])
            ->setInvoiceNumber($r[7])
            ->setDescription($r[8])
            ->setAmount($r[9])
            ->setMethod($r[10])
            ->setTransactionType($r[11])
            ->setCustomerId($r[12])
            ->setMd5Hash($r[37])
            ->setCardCodeResponseCode($r[39]);
        
        return $result;
    }
}