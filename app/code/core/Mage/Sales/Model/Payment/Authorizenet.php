<?php

class Mage_Sales_Model_Payment_Authorizenet extends Mage_Sales_Model_Payment_Ccsave
{
    public function onOrderCreate(Mage_Sales_Model_Order $order)
    {
        
    }
    
    public function onInvoiceCreate(Mage_Sales_Model_Invoice $invoice)
    {
        
    }
    
    protected function _postHttpTransaction($transaction)
    {
        $cgi = 
        $client = new Zend_Http_Client();
        $uri = ((string)$cgi->protocol).'://'.((string)$cgi->host).':'.((string)$cgi->port).((string)$cgi->url);
        $client->setUri($uri);
        $client->setParameterGet($params);
        $response = $client->request();
        $responseBody = $response->getBody();
    }
}