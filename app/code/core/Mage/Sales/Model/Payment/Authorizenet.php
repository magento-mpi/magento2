<?php

class Mage_Sales_Model_Payment_Authorizenet extends Mage_Sales_Model_Payment_Ccsave
{
    const REQUEST_DELIM_CHAR = ',';

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const REQUEST_TYPE_CREDIT = 'CREDIT';
    const REQUEST_TYPE_VOID = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';
    
    const ECHECK_ACCT_TYPE_CHECKING = 'Checking';
    const ECHECK_ACCT_TYPE_BUSINESS = 'Business Checking';
    const ECHECK_ACCT_TYPE_SAVINGS = 'Savings';
    
    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    public function onOrderCreate(Mage_Sales_Model_Order $order)
    {
        
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
    public function buildRequest(Varien_Data_Object $payment, Varien_Data_Object $billing, Varien_Data_Object $shipping)
    {
        $config = Mage::getSingleton('sales', 'config')->getPaymentConfig('authorizenet');
        
        $request = Mage::getModel('sales', 'payment_authorizenet_request')
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXDelimChar(self::REQUEST_DELIM_CHAR)
            ->setXRelayResponse('False');
        
        $request->setXTestRequest($config->is('test') ? 'TRUE' : 'FALSE');
            
        $request->setXLogin($config->login)
            ->setXTranKey($config->transKey)
            ->setXAmount($payment->getAmount())
            ->setXType($payment->getAnetTransType());
        
        switch ($payment->getAnetRequestType()) {
            case 'cc':
                $request->setXCardNum($ccNum)->setXExpDate($ccExp);
                break;
                
            case 'echeck':
                $request->setXBankAbaCode($routingNumber)->setXBankName($bankName)
                    ->setXBankAcctNum($accountNumber)->setXBankAcctType($accountType)->setXBankAcctName($accountName)
                    ->setXEcheckType($echeckType);
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

        $result = Mage::getModel('sales', 'payment_authorizenet_result');
        $result->setRawRequest($requestBody);
        $result->setRawResponse($responseBody);
        
        $result->setCode($r[0]);
        $result->setMessage($r[3]);
        $result->setApprovalCode($r[4]);
        $result->setAvsCode($r[5]);
        $result->setTransId($r[6]);
        
        return $result;
    }
}