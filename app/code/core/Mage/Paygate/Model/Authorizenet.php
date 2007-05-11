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
    
    const RESPONSE_CODE_APPROVED = 1;
    const RESPONSE_CODE_DECLINED = 2;
    const RESPONSE_CODE_ERROR = 3;
    const RESPONSE_CODE_HELD = 4;
    
    protected $_config;
    
    public function __construct()
    {
        parent::__construct();
        $this->_config = Mage::getSingleton('sales', 'config')->getPaymentConfig('authorizenet');
    }

    public function createFormBlock($name)
    {
        $hidden = array(
            'anet_trans_method'=>'CC',
        );
        
        $block = $this->getLayout()->createBlock('payment_cc_form', $name)
            ->assign('method', 'authorizenet')
            ->assign('hidden', $hidden)
            ->init($this->_payment);
        return $block;
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment_cc_info', $name)
            ->init($this->_payment);
        return $block;
    }
    
    public function onOrderValidate(Mage_Sales_Model_Order_Entity_Payment $payment)
    {
        $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);
        $request = $this->buildRequest($payment);
        $result = $this->postRequest($request);
        
        $payment->setCcApproval($result->getApprovalCode())
            ->setCcTransId($result->getTransactionId())
            ->setCcAvsStatus($result->getAvsResultCode())
            ->setCcCidStatus($result->getCardCodeResponseCode());
        
        if ($this->_config->is('test')) {
            $payment->setCcRawRequest($result->getRawRequest())
                ->setCcRawResponse($result->getRawResponse());
        }
            
        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                break;
                
            case self::RESPONSE_CODE_DECLINED:
                break;
                
            default:
                break;
        }
        
        return $this;
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Entity_Payment $payment)
    {
        foreach ($order->getEntitiesByType('transaction') as $transaction) {
            break;
        }
        if ($transaction->getAnetTransId()) {
            $transaction->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
        }
        if ($transaction->getAnetAuthCode()) {
            $transaction->setAnetTransType(self::REQUEST_TYPE_CAPTURE_ONLY);
        }
        
        $request = $this->buildRequest($transaction);
    }
    
    /**
     * Enter description here...
     *
     * @link http://www.authorize.net/support/AIM_guide.pdf
     * @param Mage_Sales_Model_Document $document
     * @return unknown
     */
    public function buildRequest(Varien_Object $payment)
    {
        $document = $payment->getDocument();
        
        if (!$payment->getAnetTransMethod()) {
            $payment->setAnetTransMethod(self::REQUEST_METHOD_CC);
        }
        
        $request = Mage::getModel('paygate', 'authorizenet_request')
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXDelimChar(self::RESPONSE_DELIM_CHAR)
            ->setXRelayResponse('False');
        
        $request->setXTestRequest($this->_config->is('test') ? 'TRUE' : 'FALSE');
            
        $request->setXLogin((string)$this->_config->login)
            ->setXTranKey((string)$this->_config->transKey)
            ->setXAmount($document->getGrandTotal())
            ->setXType($payment->getAnetTransType())
            ->setXMethod($payment->getAnetTransMethod());
            
        switch ($request->getAnetTransType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $request->setXTransId($payment->getCcTransId());
                break;
                
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setXAuthCode($payment->getCcAuthCode());
                break;
        }
            
        if (!empty($document)) {
            $request->setXInvoiceNum($document->getId());
            
            $billing = $document->getAddressByType('billing');
            if (!empty($billing)) {
                $request->setXFirstName($billing->getFirstname())
                    ->setXLastName($billing->getLastname())
                    ->setXCompany($billing->getCompany())
                    ->setXAddress($billing->getStreet(1))
                    ->setXCity($billing->getCity())
                    ->setXState($billing->getRegionName())
                    ->setXZip($billing->getPostcode())
                    ->setXCountry($billing->getCountryName())
                    ->setXPhone($billing->getPhone())
                    ->setXFax($billing->getFax())
                    ->setXCustId($billing->getCustomerId())
                    ->setXCustomerIp($billing->getRemoteIp())
                    ->setXCustomerTaxId($billing->getTaxId())
                    ->setXEmail($billing->getEmail())
                    ->setXEmailCustomer($this->_config->is('emailCustomer'))
                    ->setXMerchantEmail((string)$this->_config->merchantEmail);
            }
            
            $shipping = $document->getAddressByType('shipping');
            if (!empty($shipping)) {
                $request->setXShipToFirstName($shipping->getFirstname())
                    ->setXShipToLastName($shipping->getLastname())
                    ->setXShipToCompany($shipping->getCompany())
                    ->setXShipToAddress($shipping->getStreet(1))
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
        
        switch ($payment->getAnetTransMethod()) {
            case self::REQUEST_METHOD_CC:
                $request->setXCardNum($payment->getCcNumber())
                    ->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                    ->setXCardCode($payment->getCcCid());
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
    
    public function postRequest(Varien_Object $request)
    {
        $cgi = Mage::getSingleton('sales', 'config')->getPaymentConfig('authorizenet')->cgi;
        $client = new Zend_Http_Client();
        $uri = ((string)$cgi->protocol).'://'.((string)$cgi->host).':'.((string)$cgi->port).((string)$cgi->url);
        $client->setUri($uri);
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterPost($request->getData());
        $client->setMethod(Zend_Http_Client::POST);
        $response = $client->request();
        
        $result = Mage::getModel('paygate', 'authorizenet_result');
        
        $requestArr = array();
        foreach ($request->getData() as $key=>$value) {
            $requestArr[] = urlencode($key).'='.urlencode($value);
        }
        $requestBody = join('&', $requestArr);        
        $result->setRawRequest($requestBody);
        
        $responseBody = $response->getBody();
        $result->setRawResponse($responseBody);
        
        $r = explode(self::RESPONSE_DELIM_CHAR, $responseBody);        
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