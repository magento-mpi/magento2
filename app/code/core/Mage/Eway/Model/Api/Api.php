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
 * Eway API wrappers model
 *
 */
class Mage_Eway_Model_Api_Api extends Mage_Eway_Model_Api_Abstract
{
    public function callDoDirectPayment()
    {
        $payment = $this->getPayment();
        $billing = $this->getBillingAddress();
        
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
        $this->getQuote()->reserveOrderId();
        $xml = "<ewaygateway>";
        $xml .= "<ewayCustomerID>" . $this->getCustomerID() . "</ewayCustomerID>";
        $xml .= "<ewayTotalAmount>" . $this->getAmount() . "</ewayTotalAmount>";
        $xml .= "<ewayCardHoldersName>" . $payment->getCcName() . "</ewayCardHoldersName>";
        $xml .= "<ewayCardNumber>" . $payment->getCcNumber() . "</ewayCardNumber>";
        $xml .= "<ewayCardExpiryMonth>" . $payment->getCcExpMonth() . "</ewayCardExpiryMonth>";
        $xml .= "<ewayCardExpiryYear>" . $payment->getCcExpYear() . "</ewayCardExpiryYear>";
        $xml .= "<ewayTrxnNumber>" . '' . "</ewayTrxnNumber>";
        $xml .= "<ewayCustomerInvoiceDescription>" . $invoiceDesc . "</ewayCustomerInvoiceDescription>";
        $xml .= "<ewayCustomerFirstName>" . $billing->getFirstname() . "</ewayCustomerFirstName>";
        $xml .= "<ewayCustomerLastName>" . $billing->getLastname() . "</ewayCustomerLastName>";
        $xml .= "<ewayCustomerEmail>" . $this->getQuote()->getCustomerEmail() . "</ewayCustomerEmail>";
        $xml .= "<ewayCustomerAddress>" . trim($formatedAddress) . "</ewayCustomerAddress>";
        $xml .= "<ewayCustomerPostcode>" . $billing->getPostcode() . "</ewayCustomerPostcode>";
        $xml .= "<ewayCustomerInvoiceRef>" . $this->getQuote()->getReservedOrderId() . "</ewayCustomerInvoiceRef>";

        if ($this->getUseccv()) {
            $xml .= "<ewayCVN>" . $payment->getCvn() . "</ewayCVN>";
        }

        $xml .= "<ewayOption1>" . '' . "</ewayOption1>";
        $xml .= "<ewayOption2>" . '' . "</ewayOption2>";
        $xml .= "<ewayOption3>" . '' . "</ewayOption3>";
     	$xml .= "</ewaygateway>";
     	
     	$resultArr = $this->call($xml);
     	
     	if ($resultArr === false) {
     	    return false;
     	}

     	$this->setTransactionId($resultArr['ewayTrxnNumber']);
     	
     	return $resultArr;
    }
    
    public function call($xml)
    {
        if ($this->getDebug()) {
            $debug = Mage::getModel('eway/api_debug')
                ->setRequestBody($xml)
                ->save();
        }

        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);

        $http->setConfig($config);
        $http->write(Zend_Http_Client::POST, $this->getApiGatewayUrl(), '1.1', array(), $xml);
        $response = $http->read();
        
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($this->getDebug()) {
            $debug->setResponseBody($response)->save();
        }

        if ($http->getErrno()) {
            $http->close();
            $this->setError(array(
                'message' => $http->getError()
            ));
            return false;
        }
        $http->close();
        
        $parsedResArr = $this->parseXmlResult($response);

        if ($parsedResArr['ewayTrxnStatus'] == 'True') {
            $this->unsError();
            return $response;
        }

        if (isset($parsedResArr['ewayTrxnError'])) {
            $this->setError(array(
                'message' => $parsedResArr['ewayTrxnError']
            ));
        }
                
        return false;
    }
    
    public function parseXmlResult($xmlResponse)
    {
        /**
         * @todo check and add error if xml was not loaded
         */
        $xmlObj = simplexml_load_string($xmlResponse);
        $newResArr = array();
        foreach ($xmlObj as $key => $val) {
            $newResArr[$key] = (string)$val;
        }

        return $newResArr;
    }

}