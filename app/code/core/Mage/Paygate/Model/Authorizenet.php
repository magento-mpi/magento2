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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Paygate_Model_Authorizenet extends Mage_Payment_Model_Method_Cc
{
    const CGI_URL = 'https://secure.authorize.net/gateway/transact.dll';

    const REQUEST_METHOD_CC     = 'CC';
    const REQUEST_METHOD_ECHECK = 'ECHECK';

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY    = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const REQUEST_TYPE_CREDIT       = 'CREDIT';
    const REQUEST_TYPE_VOID         = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
    const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ECHECK_ACCT_TYPE_SAVINGS  = 'SAVINGS';

    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    const RESPONSE_DELIM_CHAR = ',';

    const RESPONSE_CODE_APPROVED = 1;
    const RESPONSE_CODE_DECLINED = 2;
    const RESPONSE_CODE_ERROR    = 3;
    const RESPONSE_CODE_HELD     = 4;

    const RESPONSE_REASON_CODE_PARTIAL_APPROVE = 295;

    const METHOD_CODE = 'authorizenet';

    protected $_code  = self::METHOD_CODE;

    /**
     * Form block type
     */
    protected $_formBlockType = 'paygate/authorizenet_form_cc';

    /**
     * Info block type
     */
    protected $_infoBlockType = 'paygate/authorizenet_info_cc';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    protected $_allowCurrencyCode = array('USD');

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('x_login', 'x_tran_key',
                                                    'x_card_num', 'x_exp_date',
                                                    'x_card_code', 'x_bank_aba_code',
                                                    'x_bank_name', 'x_bank_acct_num',
                                                    'x_bank_acct_type','x_bank_acct_name',
                                                    'x_echeck_type');

    /**
     * Key for storing transaction id in additional information of payment model
     * @var string
     */
    protected $_realTransactionIdKey = 'x_transaction_id';

    /**
     * Key for storing split tender id in additional information of payment model
     * @var string
     */
    protected $_splitTenderIdKey = 'split_tender_id';

    /**
     * Key for storing partial authorization confirmation message in session
     * @var string
     */
    protected $_sessionPartialAuthorizationConfirmationMessageKey = 'paygate_authorizenet_confirmation_message';

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Set partial authorization confirmation message into session
     *
     * @param string $message
     * @return Mage_Paygate_Model_Authorizenet
     */
    public function setPartialAuthorizationConfirmationMessage($message)
    {
        $this->_getSession()->setData($this->_sessionPartialAuthorizationConfirmationMessageKey, $message);
        return $this;
    }

    /**
     * Return partial authorization confirmation message from session
     *
     * @return string
     */
    public function getPartialAuthorizationConfirmationMessage()
    {
        return $this->_getSession()->getData($this->_sessionPartialAuthorizationConfirmationMessageKey);
    }

    /**
     * Unset partial authorization confirmation message in session
     *
     * @return Mage_Paygate_Model_Authorizenet
     */
    public function unsetPartialAuthorizationConfirmationMessage()
    {
        $this->_getSession()->setData($this->_sessionPartialAuthorizationConfirmationMessageKey, false);
        return $this;
    }

    /**
     * Send authorize request to gateway
     *
     * @param  Varien_Object $payment
     * @param  decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount for authorization.'));
        }

        $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);

        if ($this->isItPartialAuthorization($payment)) {
            $amount = $amount - $this->getCardsInstance($payment)->getProcessedAmount();
            if ($amount <= 0) {
                Mage::throwException(Mage::helper('paygate')->__('Invalid amount for partial authorization.'));
            }

            $payment->setAmount($amount);
            $request= $this->_buildRequest($payment);
            $result = $this->_postRequest($request);

            $this->_processPartialAuthorizationResponse($result, $payment);

            return $this;
        }

        $payment->setAmount($amount);
        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                $payment
                    ->setTransactionId($result->getTransactionId())
                    ->setIsTransactionClosed(0)
                    ->setTransactionAdditionalInfo($this->_realTransactionIdKey, $result->getTransactionId());
                $this->_registerCard($result, $payment);
                return $this;
            case self::RESPONSE_CODE_HELD:
                if ($this->_processPartialAuthorizationResponse($result, $payment)) {
                    return $this;
                }
                Mage::throwException(Mage::helper('paygate')->__('Payment authorization error.'));
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($result->getResponseReasonText()));
            default:
                Mage::throwException(Mage::helper('paygate')->__('Payment authorization error.'));
        }
    }

    /**
     * Send capture request to gateway
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount for capture.'));
        }

        $payment->setAmount($amount);

        if ($payment->getParentTransactionId()) {
            $payment->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
            $payment->setXTransId($this->_getRealParentTransactionId($payment));
        } else {
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }

        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                if (!$payment->getParentTransactionId() || $result->getTransactionId() != $payment->getParentTransactionId()) {
                    $payment->setTransactionId($result->getTransactionId());
                }
                $payment
                    ->setIsTransactionClosed(0)
                    ->setTransactionAdditionalInfo($this->_realTransactionIdKey, $result->getTransactionId());
                return $this;
            case self::RESPONSE_CODE_HELD:
                $this->_processPartialAuthorization($result, $payment);
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($result->getResponseReasonText()));
            default:
                Mage::throwException(Mage::helper('paygate')->__('Payment capturing error.'));
        }
    }


    /**
     * Void the payment through gateway
     *
     * @param Varien_Object $payment
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    public function void(Varien_Object $payment)
    {
        if (!$payment->getParentTransactionId()) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid transaction ID.'));
        }

        $payment->setAnetTransType(self::REQUEST_TYPE_VOID);
        $payment->setXTransId($this->_getRealParentTransactionId($payment));

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                if ($result->getTransactionId() != $payment->getParentTransactionId()) {
                    $payment->setTransactionId($result->getTransactionId());
                }
                $payment
                    ->setIsTransactionClosed(1)
                    ->setShouldCloseParentTransaction(1)
                    ->setTransactionAdditionalInfo($this->_realTransactionIdKey, $result->getTransactionId());
                return $this;
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($result->getResponseReasonText()));
            default:
                Mage::throwException(Mage::helper('paygate')->__('Payment voiding error.'));
        }
        return $this;
    }

    /**
     * refund the amount with transaction id
     *
     * @param string $payment Varien_Object object
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount for refund.'));
        }

        if (!$payment->getParentTransactionId()) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid transaction ID.'));
        }

        $payment->setAnetTransType(self::REQUEST_TYPE_CREDIT);
        $payment->setAmount($amount);
        $payment->setXTransId($this->_getRealParentTransactionId($payment));

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                if ($result->getTransactionId() != $payment->getParentTransactionId()) {
                    $payment->setTransactionId($result->getTransactionId());
                }
                $payment
                     ->setIsTransactionClosed(1)
                     ->setShouldCloseParentTransaction(1)
                     ->setTransactionAdditionalInfo($this->_realTransactionIdKey, $result->getTransactionId());
                return $this;
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($result->getResponseReasonText()));
            default:
                Mage::throwException(Mage::helper('paygate')->__('Payment refunding error.'));
        }
    }

    /**
     * Cancel partial authorizations and flush current split_tender_id record
     *
     * @param Mage_Payment_Model_Info $payment
     */
    public function cancelAuthorizations(Mage_Payment_Model_Info $payment) {
        if (!$payment->getAdditionalInformation($this->_splitTenderIdKey)) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid transaction ID.'));
        }

        $request = $this->_getRequest();
        $request->setXSplitTenderId($payment->getAdditionalInformation($this->_splitTenderIdKey));

        $request
            ->setXType(self::REQUEST_TYPE_VOID)
            ->setXMethod(self::REQUEST_METHOD_CC);

        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                $payment->setAdditionalInformation($this->_splitTenderIdKey, null);
                $this->getCardsInstance($payment)->flushCards();
                return;
            default:
                Mage::throwException(Mage::helper('paygate')->__('Payment refunding error.'));
        }

    }

    /**
     * Return authorize payment request
     *
     * @return Mage_Paygate_Model_Authorizenet_Request
     */
    protected function _getRequest()
    {
        $request = Mage::getModel('paygate/authorizenet_request')
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXDelimChar(self::RESPONSE_DELIM_CHAR)
            ->setXRelayResponse('False')
            ->setXTestRequest($this->getConfigData('test') ? 'TRUE' : 'FALSE')
            ->setXLogin($this->getConfigData('login'))
            ->setXTranKey($this->getConfigData('trans_key'));

        return $request;
    }

    /**
     * Prepare request to gateway
     *
     * @link http://www.authorize.net/support/AIM_guide.pdf
     * @param Mage_Sales_Model_Document $order
     * @return unknown
     */
    protected function _buildRequest(Varien_Object $payment)
    {
        $order = $payment->getOrder();

        $this->setStore($order->getStoreId());

        if (!$payment->getAnetTransMethod()) {
            $payment->setAnetTransMethod(self::REQUEST_METHOD_CC);
        }

        $request = $this->_getRequest()
            ->setXType($payment->getAnetTransType())
            ->setXMethod($payment->getAnetTransMethod());

        if ($order && $order->getIncrementId()) {
            $request->setXInvoiceNum($order->getIncrementId());
        }

        if($payment->getAmount()){
            $request->setXAmount($payment->getAmount(),2);
            $request->setXCurrencyCode($order->getBaseCurrencyCode());
        }

        switch ($payment->getAnetTransType()) {
            case self::REQUEST_TYPE_AUTH_CAPTURE:
                $request->setXAllowPartialAuth($this->getConfigData('allow_partial_authorization') ? 'True' : 'False');
                if ($payment->getAdditionalInformation($this->_splitTenderIdKey)) {
                    $request->setXSplitTenderId($payment->getAdditionalInformation($this->_splitTenderIdKey));
                }
                break;
            case self::REQUEST_TYPE_AUTH_ONLY:
                $request->setXAllowPartialAuth($this->getConfigData('allow_partial_authorization') ? 'True' : 'False');
                if ($payment->getAdditionalInformation($this->_splitTenderIdKey)) {
                    $request->setXSplitTenderId($payment->getAdditionalInformation($this->_splitTenderIdKey));
                }
                break;
            case self::REQUEST_TYPE_CREDIT:
                /**
                 * need to send last 4 digit credit card number to authorize.net
                 * otherwise it will give an error
                 */
                $request->setXCardNum($payment->getCcLast4());
                $request->setXTransId($payment->getXTransId());
                break;
            case self::REQUEST_TYPE_VOID:
                $request->setXTransId($payment->getXTransId());
                break;
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $request->setXTransId($payment->getXTransId());
                break;
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setXAuthCode($payment->getCcAuthCode());
                break;
        }

        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setXFirstName($billing->getFirstname())
                    ->setXLastName($billing->getLastname())
                    ->setXCompany($billing->getCompany())
                    ->setXAddress($billing->getStreet(1))
                    ->setXCity($billing->getCity())
                    ->setXState($billing->getRegion())
                    ->setXZip($billing->getPostcode())
                    ->setXCountry($billing->getCountry())
                    ->setXPhone($billing->getTelephone())
                    ->setXFax($billing->getFax())
                    ->setXCustId($billing->getCustomerId())
                    ->setXCustomerIp($order->getRemoteIp())
                    ->setXCustomerTaxId($billing->getTaxId())
                    ->setXEmail($order->getCustomerEmail())
                    ->setXEmailCustomer($this->getConfigData('email_customer'))
                    ->setXMerchantEmail($this->getConfigData('merchant_email'));
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $request->setXShipToFirstName($shipping->getFirstname())
                    ->setXShipToLastName($shipping->getLastname())
                    ->setXShipToCompany($shipping->getCompany())
                    ->setXShipToAddress($shipping->getStreet(1))
                    ->setXShipToCity($shipping->getCity())
                    ->setXShipToState($shipping->getRegion())
                    ->setXShipToZip($shipping->getPostcode())
                    ->setXShipToCountry($shipping->getCountry());
            }

            $request->setXPoNum($payment->getPoNumber())
                ->setXTax($order->getBaseTaxAmount())
                ->setXFreight($order->getBaseShippingAmount());
        }

        switch ($payment->getAnetTransMethod()) {
            case self::REQUEST_METHOD_CC:
                if($payment->getCcNumber()){
                    $request->setXCardNum($payment->getCcNumber())
                        ->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                        ->setXCardCode($payment->getCcCid());
                }
                if ($this->getConfigData('partialauth')) {
                    $request->setXAllowPartialAuth('true');
                    $splitTenderId = $this->_getQuote()->getPayment()->getAdditionalInformation('split_tender_id');
                    if ($splitTenderId) {
                        $request->setSplitTenderId($splitTenderId);
                    }
                }
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

    protected function _postRequest(Varien_Object $request)
    {
        $debugData = array('request' => $request->getData());

        $result = Mage::getModel('paygate/authorizenet_result');

        $client = new Varien_Http_Client();

        $uri = $this->getConfigData('cgi_url');
        $client->setUri($uri ? $uri : self::CGI_URL);
        $client->setConfig(array(
            'maxredirects'=>0,
            'timeout'=>30,
            //'ssltransport' => 'tcp',
        ));
        $client->setParameterPost($request->getData());
        $client->setMethod(Zend_Http_Client::POST);

        try {
            $response = $client->request();
        } catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());

            $debugData['result'] = $result->getData();
            $this->_debug($debugData);
            Mage::throwException($this->_wrapGatewayError($e->getMessage()));
        }

        $responseBody = $response->getBody();

        $r = explode(self::RESPONSE_DELIM_CHAR, $responseBody);

        if ($r) {
            $result->setResponseCode((int)str_replace('"','',$r[0]))
                ->setResponseSubcode((int)str_replace('"','',$r[1]))
                ->setResponseReasonCode((int)str_replace('"','',$r[2]))
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
                ->setCardCodeResponseCode($r[38])
                ->setCAVVResponseCode( (isset($r[39])) ? $r[39] : null)
                ->setSplitTenderId($r[52])
                ->setAccNumber($r[50])
                ->setCardType($r[51])
                ->setRequestedAmount($r[53])
                ->setBalanceOnCard($r[54])
                ;
        }
        else {
             Mage::throwException(
                Mage::helper('paygate')->__('Error in payment gateway.')
            );
        }

        $debugData['result'] = $result->getData();
        $this->_debug($debugData);

        return $result;
    }

    /**
     * Gateway response wrapper
     *
     * @param string $text
     * @return string
     */
    protected function _wrapGatewayError($text)
    {
        return Mage::helper('paygate')->__('Gateway error: %s', $text);
    }

    /**
     * Return additional information`s transaction_id value of parent transaction model
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return array|null|mixed
     */
    protected function _getRealParentTransactionId($payment)
    {
        $transaction = $payment->getTransaction($payment->getParentTransactionId());
        return $transaction->getAdditionalInformation($this->_realTransactionIdKey);
    }

    /**
     * Return current quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Core_Model_Session_Abstract
     */
    protected function _getSession()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote');
        } else {
            return Mage::getSingleton('checkout/session');
        }
    }

    /**
     * Set split_tender_id to quote payment if neeeded
     *
     * @param Varien_Object $response
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return bool
     */
    protected function _processPartialAuthorizationResponse(Varien_Object $response, Mage_Sales_Model_Order_Payment $orderPayment)
    {
        if (!$response->getSplitTenderId()) {
            return false;
        }

        $exceptionMessage = null;
        $isPartialAuthorizationProcessCompleted = false;
        $isLastPartialAuthorizationSuccessful = false;

        try {
            $orderPayment->setAdditionalInformation($this->_splitTenderIdKey, $response->getSplitTenderId());
            switch ($response->getResponseCode()) {
                case self::RESPONSE_CODE_HELD:
                    if ($response->getResponseReasonCode() != self::RESPONSE_REASON_CODE_PARTIAL_APPROVE) {
                        return false;
                    }
                    $this->_registerCard($response, $orderPayment);
                    $isLastPartialAuthorizationSuccessful = true;
                    break;
                case self::RESPONSE_CODE_APPROVED:
                    /* temp */ $orderPayment->setTransactionId($response->getTransactionId())->setIsTransactionClosed(0)->setTransactionAdditionalInfo($this->_realTransactionIdKey, $response->getTransactionId());
                    $this->_registerCard($response, $orderPayment);
                    $isLastPartialAuthorizationSuccessful = true;
                    $isPartialAuthorizationProcessCompleted = true;
                    break;
                case self::RESPONSE_CODE_DECLINED:
                case self::RESPONSE_CODE_ERROR:
                    $exceptionMessage = $this->_wrapGatewayError($response->getResponseReasonText());
                    break;
                default:
                    $exceptionMessage = $this->_wrapGatewayError(Mage::helper('paygate')->__('Payment partial authorization error.'));
            }
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
        }

        if (!$isPartialAuthorizationProcessCompleted) {
            if ($isLastPartialAuthorizationSuccessful) {
                $this->setPartialAuthorizationConfirmationMessage(Mage::helper('paygate')->__('The amount on your credit card is insufficient to complete your purchase. The available amount has been put on hold. To complete your purchase click OK and specify additional credit card number. To cancel the purchase and release the amount on hold, click Cancel.'));
            } else {
                $this->setPartialAuthorizationConfirmationMessage(Mage::helper('paygate')->__('Your credit card has been declined. Click Continue to specify another credit card to complete your purchase. Click Cancel to release the amount on hold and select another payment method.'));
            }

            $quotePayment = $this->_getQuote()->getPayment();
            $quotePayment->setAdditionalInformation($orderPayment->getAdditionalInformation());
            throw new Mage_Payment_Model_Info_Exception($exceptionMessage);
        }

        return true;
    }

    protected function _registerCard(Varien_Object $response, Mage_Sales_Model_Order_Payment $payment)
    {
        $cardInfo = array(
                'cc_number'         => $response->getAccNumber(),
                'cc_type'           => $response->getCardType(),
                'requested_amount'  => $response->getRequestedAmount(),
                'balance_on_card'   => $response->getBalanceOnCard(),
                'auth_id'           => $response->getTransactionId(),
                'processed_amount'  => $response->getAmount()
            );
        $this->getCardsInstance($payment)->addCard($cardInfo);
    }

    /**
     * Get cards model instance
     *
     * @param mixed $payment
     * @return Mage_Paygate_Model_Authorizenet_Cards
     */
    public function getCardsInstance($payment = null)
    {
        if (is_null($payment)) {
            $payment = $this->getInfoInstance();
        }
        return Mage::getModel('paygate/authorizenet_cards')
            ->setPayment($payment);
    }

    public function isItPartialAuthorization($payment = null)
    {
        if (is_null($payment)) {
            $payment = $this->getInfoInstance();
        }
        return $payment->getAdditionalInformation($this->_splitTenderIdKey);
    }
}
