<?php

class Mage_Sales_Model_Payment_Authorizenet extends Mage_Sales_Model_Payment_Ccsave
{
    public function onOrderCreate(Mage_Sales_Model_Order $order)
    {
        
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice $invoice)
    {
        
    }
    
    protected function _performDocumentTransaction(Mage_Sales_Model_Document $document)
    {
        $request = Mage::getModel('sales', 'payment_authorizenet_request');
        
       
    }
    
    protected function _postHttpTransaction(Varien_Data_Object $request)
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
        
        $result = Mage::getModel('sales', 'payment_authorizenet_result');
        $result->setRawRequest($requestBody);
        $result->setRawResponse($responseBody);
        
        $responseArr = explode(',', $responseBody);
        
        return $responseBody;
    }
}