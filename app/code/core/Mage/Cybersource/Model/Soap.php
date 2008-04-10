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
 * @package    Mage_cybersource
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Cybersource_Model_Soap extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'cybersource_soap';

    const WSDL_URL_TEST = 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl';
    const WSDL_URL_LIVE = 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl';

    const RESPONSE_CODE_SUCCESS = 100;

    /**
     * Availability options
    */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    protected $_request;

    public function validate()
    {
        if (!extension_loaded('soap')) {
            Mage::throwException(Mage::helper('cybersource')->__('SOAP extension is not enabled. Please contact us.'));
        }
        parent::validate();
    }

    protected function getSoapApi($options = array())
    {
        $wsdl = $this->getConfigData('test') ? self::WSDL_URL_TEST  : self::WSDL_URL_LIVE;
        return new Mage_Cybersource_Model_Api_ExtendedSoapClient($wsdl, $options);
    }

    protected function iniRequest()
    {
        $this->_request = new stdClass();
        $this->_request->merchantID = $this->getConfigData('merchant_id');
        $this->_request->merchantReferenceCode = $this->_generateReferenceCode();

        $this->_request->clientLibrary = "PHP";
        $this->_request->clientLibraryVersion = phpversion();
        $this->_request->clientEnvironment = php_uname();

    }

    protected function _generateReferenceCode()
    {
        return md5(microtime() . rand(0, time()));
    }

    protected function addBillingAddress($billing, $email)
    {
        $billTo = new stdClass();
        $billTo->firstName = $billing->getFirstname();
        $billTo->lastName = $billing->getLastname();
        $billTo->street1 = $billing->getStreet(1);
        $billTo->street2 = $billing->getStreet(2);
        $billTo->city = $billing->getCity();
        $billTo->state = $billing->getRegion();
        $billTo->postalCode = $billing->getPostcode();
        $billTo->country = $billing->getCountry();
        $billTo->phoneNumber = $billing->getTelephone();
        $billTo->email = $email;
        $this->_request->billTo = $billTo;
    }

    protected function addShippingAddress($shipping)
    {
        $shipTo = new stdClass();
        $shipTo->firstName = $shipping->getFirstname();
        $shipTo->lastName = $shipping->getLastname();
        $shipTo->street1 = $shipping->getStreet(1);
        $shipTo->street2 = $shipping->getStreet(2);
        $shipTo->city = $shipping->getCity();
        $shipTo->state = $shipping->getRegion();
        $shipTo->postalCode = $shipping->getPostcode();
        $shipTo->country = $shipping->getCountry();
        $shipTo->phoneNumber = $shipping->getTelephone();
        $this->_request->shipTo = $shipTo;
    }

    protected function addCcInfo($payment)
    {
        $card = new stdClass();
    	$card->accountNumber = $payment->getCcNumber();
    	$card->expirationMonth = $payment->getCcExpMonth();
    	$card->expirationYear =  $payment->getCcExpYear();
    	if ($payment->hasCcCid()) {
    	    $card->cvNumber =  $payment->getCcCid();
    	}
    	$this->_request->card = $card;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $error = false;

        $soapClient = $this->getSoapApi();

        $this->iniRequest();

        $ccAuthService = new stdClass();
    	$ccAuthService->run = "true";
    	$this->_request->ccAuthService = $ccAuthService;
    	$this->addBillingAddress($payment->getOrder()->getBillingAddress(), $payment->getOrder()->getCustomerEmail());
    	$this->addShippingAddress($payment->getOrder()->getShippingAddress());
    	$this->addCcInfo($payment);

    	$purchaseTotals = new stdClass();
    	$purchaseTotals->currency = $payment->getOrder()->getBaseCurrencyCode();
    	$purchaseTotals->grandTotalAmount = $amount;
    	$this->_request->purchaseTotals = $purchaseTotals;

        try {
        	$result = $soapClient->runTransaction($this->_request);
        	if ($result->reasonCode==self::RESPONSE_CODE_SUCCESS) {
                $payment->setLastTransId($result->requestID)
                    ->setCcTransId($result->requestID)
                    ->setCybersourceToken($result->requestToken)
                    ->setCcAvsStatus($result->ccAuthReply->avsCode)
                    ->setCcCidStatus($result->ccAuthReply->cvCode);
        	} else {
                 $error = Mage::helper('cybersource')->__('There is an error in processing payment. Please try again or contact us.');
        	}

        } catch (Exception $e) {
           Mage::throwException(
                Mage::helper('cybersource')->__('Gateway request error: %s', $e->getMessage())
            );
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $error = false;
        $soapClient = $this->getSoapApi();
        $this->iniRequest();

        if ($payment->getCcTransId() && $payment->getCybersourceToken()) {
            $ccCaptureService = new stdClass();
    	    $ccCaptureService->run = "true";
    	    $ccCaptureService->authRequestToken = $payment->getCybersourceToken();
    	    $ccCaptureService->authRequestID = $payment->getCcTransId();
    	    $this->_request->ccCaptureService = $ccCaptureService;

    	    $item0 = new stdClass();
        	$item0->unitPrice = $amount;
        	$item0->id = 0;
            $this->_request->item = array($item0);
        } else {
            $ccAuthService = new stdClass();
            $ccAuthService->run = "true";
            $this->_request->ccAuthService = $ccAuthService;

            $ccCaptureService = new stdClass();
            $ccCaptureService->run = "true";
            $this->_request->ccCaptureService = $ccCaptureService;

            $this->addBillingAddress($payment->getOrder()->getBillingAddress(), $payment->getOrder()->getCustomerEmail());
            $this->addShippingAddress($payment->getOrder()->getShippingAddress());
            $this->addCcInfo($payment);

            $purchaseTotals = new stdClass();
            $purchaseTotals->currency = $payment->getOrder()->getBaseCurrencyCode();
            $purchaseTotals->grandTotalAmount = $amount;
            $this->_request->purchaseTotals = $purchaseTotals;
        }
        try {
        	$result = $soapClient->runTransaction($this->_request);
        	if ($result->reasonCode==self::RESPONSE_CODE_SUCCESS) {
        	    /*
        	    for multiple capture we need to use the latest capture transaction id
        	    */
                $payment->setLastTransId($result->requestID)
                    ->setLastCybersourceToken($result->requestToken)
                    ->setCcTransId($result->requestID)
                    ->setCybersourceToken($result->requestToken)
                ;
        	} else {
                 $error = Mage::helper('cybersource')->__('There is an error in processing payment. Please try again or contact us.');
        	}
        } catch (Exception $e) {
           Mage::throwException(
                Mage::helper('cybersource')->__('Gateway request error: %s', $e->getMessage())
            );
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    public function processInvoice($invoice, $payment)
    {
        parent::processInvoice($invoice, $payment);
        $invoice->setCybersourceToken($payment->getLastCybersourceToken());
        return $this;
    }

    public function processBeforeVoid($invoice, $payment)
    {
        parent::processBeforeVoid($invoice, $payment);
        $payment->setVoidCybersourceToken($invoice->getCybersourceToken());
        return $this;
    }

    /*
    * we call void method only from invoice and credit memo
    * in invoice and credit memo, we save transaction in transactionid
    */
    public function void(Varien_Object $payment)
    {
        $error = false;
        if ($payment->getVoidTransactionId() && $payment->getVoidCybersourceToken()) {
            $soapClient = $this->getSoapApi();
            $this->iniRequest();
            $voidService = new stdClass();
    	    $voidService->run = "true";
    	    $voidService->voidRequestToken = $payment->getVoidCybersourceToken();
    	    $voidService->voidRequestID = $payment->getVoidTransactionId();
    	    $this->_request->voidService = $voidService;
    	    try {
        	    $result = $soapClient->runTransaction($this->_request);
                if ($result->reasonCode==self::RESPONSE_CODE_SUCCESS) {
                    $payment->setLastTransId($result->requestID)
                        ->setCcTransId($result->requestID)
                        ->setCybersourceToken($result->requestToken)
                        ;
            	} else {
                     $error = Mage::helper('cybersource')->__('There is an error in processing payment. Please try again or contact us.');
            	}
            } catch (Exception $e) {
               Mage::throwException(
                    Mage::helper('cybersource')->__('Gateway request error: %s', $e->getMessage())
                );
            }
         }else{
            $error = Mage::helper('cybersource')->__('Invalid transaction id or token');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    public function processBeforeRefund($invoice, $payment)
    {
        parent::processBeforeRefund($invoice, $payment);
        $payment->setRefundCybersourceToken($invoice->getCybersourceToken());
        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        $error = false;
        if ($payment->getRefundTransactionId() && $payment->getRefundCybersourceToken() && $amount>0) {
            $soapClient = $this->getSoapApi();
            $this->iniRequest();
            $ccCreditService = new stdClass();
    	    $ccCreditService->run = "true";
    	    $ccCreditService->captureRequestToken = $payment->getCybersourceToken();
    	    $ccCreditService->captureRequestID = $payment->getCcTransId();
    	    $this->_request->ccCreditService = $ccCreditService;

    	    $purchaseTotals = new stdClass();
            $purchaseTotals->grandTotalAmount = $amount;
            $this->_request->purchaseTotals = $purchaseTotals;

            try {
        	    $result = $soapClient->runTransaction($this->_request);
                if ($result->reasonCode==self::RESPONSE_CODE_SUCCESS) {
                    $payment->setLastTransId($result->requestID)
                        ->setLastCybersourceToken($result->requestToken)
                        ;
            	} else {
                     $error = Mage::helper('cybersource')->__('There is an error in processing payment. Please try again or contact us.');
            	}
            } catch (Exception $e) {
               Mage::throwException(
                    Mage::helper('cybersource')->__('Gateway request error: %s', $e->getMessage())
                );
            }
        } else {
            $error = Mage::helper('cybersource')->__('Error in refunding the payment');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    public function processCreditmemo($creditmemo, $payment)
    {
        parent::processCreditmemo($creditmemo, $payment);
        $creditmemo->setCybersourceToken($payment->getLastCybersourceToken());
        return $this;
    }
}