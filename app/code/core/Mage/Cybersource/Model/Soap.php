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
    protected $_formBlockType = 'cybersource/form';
    protected $_infoBlockType = 'cybersource/info';

    const WSDL_URL_TEST = 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl';
    const WSDL_URL_LIVE = 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl';

    const RESPONSE_CODE_SUCCESS = 100;

    const CC_CARDTYPE_SS = 'SS';

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

        /*
    * overwrites the method of Mage_Payment_Model_Method_Cc
    * for switch or solo card
    */
    public function OtherCcType($type)
    {
        return (parent::OtherCcType($type) || $type==self::CC_CARDTYPE_SS || $type=='JCB' || $type=='UATP');
    }

    /**
     * overwrites the method of Mage_Payment_Model_Method_Cc
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        parent::assignData($data);
        $info = $this->getInfoInstance();

        if ($data->getCcType()==self::CC_CARDTYPE_SS) {
            $info->setCcSsIssue($data->getCcSsIssue())
                ->setCcSsStartMonth($data->getCcSsStartMonth())
                ->setCcSsStartYear($data->getCcSsStartYear())
            ;
        }
        return $this;
    }

    public function validate()
    {
        if (!extension_loaded('soap')) {
            Mage::throwException(Mage::helper('cybersource')->__('SOAP extension is not enabled. Please contact us.'));
        }
        /**
        * to validate paymene method is allowed for billing country or not
        */
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $billingCountry = $paymentInfo->getOrder()->getBillingAddress()->getCountryId();
        } else {
            $billingCountry = $paymentInfo->getQuote()->getBillingAddress()->getCountryId();
        }
        if (!$this->canUseForCountry($billingCountry)) {
            Mage::throwException($this->_getHelper()->__('Selected payment type is not allowed for billing country.'));
        }

        $info = $this->getInfoInstance();
        $errorMsg = false;
        $availableTypes = explode(',',$this->getConfigData('cctypes'));

        $ccNumber = $info->getCcNumber();

        // remove credit card number delimiters such as "-" and space
        $ccNumber = preg_replace('/[\-\s]+/', '', $ccNumber);
        $info->setCcNumber($ccNumber);

        $ccType = '';

        if (!$this->_validateExpDate($info->getCcExpYear(), $info->getCcExpMonth())) {
            $errorCode = 'ccsave_expiration,ccsave_expiration_yr';
            $errorMsg = $this->_getHelper()->__('Incorrect credit card expiration date');
        }

        if (in_array($info->getCcType(), $availableTypes)){
            if ($this->validateCcNum($ccNumber)
                // Other credit card type number validation
                || ($this->OtherCcType($info->getCcType()) && $this->validateCcNumOther($ccNumber))) {

                $ccType = 'OT';
                $ccTypeRegExpList = array(
                    'VI' => '/^4[0-9]{12}([0-9]{3})?$/', // Visa
                    'MC' => '/^5[1-5][0-9]{14}$/',       // Master Card
                    'AE' => '/^3[47][0-9]{13}$/',        // American Express
                    'DI' => '/^6011[0-9]{12}$/',          // Discovery
                    'JCB' => '/^(3[0-9]{15}|(2131|1800)[0-9]{12})$/', // JCB
                    'LASER' => '/^(6304|6706|6771|6709)[0-9]{12}([0-9]{3})?$/' // LASER
                );

                foreach ($ccTypeRegExpList as $ccTypeMatch=>$ccTypeRegExp) {
                    if (preg_match($ccTypeRegExp, $ccNumber)) {
                        $ccType = $ccTypeMatch;
                        break;
                    }
                }

                if (!$this->OtherCcType($info->getCcType()) && $ccType!=$info->getCcType()) {
                    $errorCode = 'ccsave_cc_type,ccsave_cc_number';
                    $errorMsg = $this->_getHelper()->__('Credit card number mismatch with credit card type');
                }
            }
            else {
                $errorCode = 'ccsave_cc_number';
                $errorMsg = $this->_getHelper()->__('Invalid Credit Card Number');
            }

        }
        else {
            $errorCode = 'ccsave_cc_type';
            $errorMsg = $this->_getHelper()->__('Credit card type is not allowed for this payment method');
        }

        if($errorMsg){
            Mage::throwException($errorMsg);
        }
        return $this;
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

    protected function getIpAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    protected function addBillingAddress($billing, $email)
    {
        if (!$email) {
            $email = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getEmail();
        }
        $billTo = new stdClass();
        $billTo->firstName = $billing->getFirstname();
        $billTo->lastName = $billing->getLastname();
        $billTo->company = $billing->getCompany();
        $billTo->street1 = $billing->getStreet(1);
        $billTo->street2 = $billing->getStreet(2);
        $billTo->city = $billing->getCity();
        $billTo->state = $billing->getRegion();
        $billTo->postalCode = $billing->getPostcode();
        $billTo->country = $billing->getCountry();
        $billTo->phoneNumber = $billing->getTelephone();
        $billTo->email = ($email ? $email : Mage::getStoreConfig('trans_email/ident_general/email'));
        $billTo->ipAddress = $this->getIpAddress();
        $this->_request->billTo = $billTo;
    }

    protected function addShippingAddress($shipping)
    {
        $shipTo = new stdClass();
        $shipTo->firstName = $shipping->getFirstname();
        $shipTo->lastName = $shipping->getLastname();
        $shipTo->company = $shipping->getCompany();
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
        $card->fullName = $payment->getCcOwner();
    	$card->accountNumber = $payment->getCcNumber();
    	$card->expirationMonth = $payment->getCcExpMonth();
    	$card->expirationYear =  $payment->getCcExpYear();
    	if ($payment->hasCcCid()) {
    	    $card->cvNumber =  $payment->getCcCid();
    	}
    	if ($payment->hasCcSsIssue()) {
    	    $card->issueNumber =  $payment->getCcSsIssue();
    	}
    	if ($payment->hasCcSsStartYear()) {
    	    $card->startMonth =  $payment->getCcSsStartMonth();
    	    $card->startYear =  $payment->getCcSsStartYear();
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