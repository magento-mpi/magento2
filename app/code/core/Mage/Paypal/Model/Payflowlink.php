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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Link payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Payflowlink extends Mage_Paypal_Model_Payflowpro
{
    /**
     * Payment method code
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK;

    protected $_formBlockType = 'paypal/payflow_link_form';
    protected $_infoBlockType = 'paypal/payflow_link_info';

    /**
     * Availability options
     */
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    /**
     * Request & response model
     * @var Mage_Paypal_Model_Payflow_Request
     */
    protected $_response;

    /**
     * Gateway request URL
     * @var string
     */
    const TRANSACTION_PAYFLOW_URL = 'https://payflowlink.paypal.com/';

    /**
     * Error message
     * @var string
     */
    const RESPONSE_ERROR_MSG = 'Payment error. %s was not found.';

    /**
     * Key for storing secure hash in additional information of payment model
     *
     * @var string
     */
    protected $_secureSilentPostHashKey = 'secure_silent_post_hash';

    /**
     * Do not validate payment form using server methods
     *
     * @return  bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = Mage::app()->getStore($this->getStore())->getId();
        $config = Mage::getModel('paypal/config')->setStoreId($storeId);
        if (Mage_Payment_Model_Method_Abstract::isAvailable($quote) && $config->isMethodAvailable($this->getCode())) {
            return true;
        }
        return false;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();

        $this->_generateSecureSilentPostHash($payment);
        $request = $this->_buildTokenRequest($payment);
        $response = $this->_postRequest($request);
        $this->_processTokenErrors($response, $payment);
    }

    /**
     * Authorize payment
     *
     * @param Mage_Sales_Model_Order_Payment | Mage_Sales_Model_Quote_Payment $payment
     * @param mixed $amount
     * @return Mage_Paypal_Model_Payflowlink
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $txnId = $payment->getAdditionalInformation('authorization_id');
        /** @var $transaction Mage_Paypal_Model_Payment_Transaction */
        $transaction =  Mage::getModel('paypal/payment_transaction');
        $transaction->loadByTxnId($txnId);
        if (!$transaction->getId()) {
            Mage::throwException(Mage::helper('paypal')->__('Shopping cart contents has been changed.'));
        }

        $amt = $transaction->getAdditionalInformation('amt');

        if (!$amt || $amt != $amount) {
            Mage::throwException(Mage::helper('paypal')->__('Shopping cart contents has been changed.'));
        }

        $payment->setTransactionId($txnId)->setIsTransactionClosed(0);
        if ($payment->getAdditionalInformation('paypal_fraud_filters') !== null) {
            $payment->setIsTransactionPending(true);
            $payment->setIsFraudDetected(true);
        }

        $transaction->delete();
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Mage_Sales_Model_Order_Payment | Mage_Sales_Model_Quote_Payment $payment
     * @param mixed $amount
     * @return Mage_Paypal_Model_Payflowlink
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $txnId = $payment->getAdditionalInformation('authorization_id');
        /** @var $transaction Mage_Paypal_Model_Payment_Transaction */
        $transaction =  Mage::getModel('paypal/payment_transaction');
        $transaction->loadByTxnId($txnId);
        if (!$transaction->getId()) {
            Mage::throwException(Mage::helper('paypal')->__('Shopping cart contents has been changed.'));
        }

        $amt = $transaction->getAdditionalInformation('amt');

        if (!$amt || $amt != $amount) {
            Mage::throwException(Mage::helper('paypal')->__('Shopping cart contents has been changed.'));
        }

        $payment->setTransactionId($txnId);
        $payment->authorize(false, $amt);
        $payment->unsTransactionId();

        $payment->setParentTransactionId($txnId);
        parent::capture($payment, $amount);

        $transaction->delete();
        return $this;
    }

    /**
     * Void payment
     *
     * @param Varien_Object $payment
     * @return Mage_Paypal_Model_Payflowlink
     */
    public function void(Varien_Object $payment)
    {
        if ($payment instanceof Mage_Sales_Model_Order_Payment) {
            parent::void($payment);
            return $this;
        }
        $request = $this->_buildBasicRequest($payment);
        $request->setTrxtype(self::TRXTYPE_DELAYED_VOID);



        $request->setOrigid($payment->getTransactionId());
        $response = $this->_postRequest($request);
        $this->_processErrors($response);
        return $this;
    }

    /**
     * Return response model.
     *
     * @return Mage_Mage_Paypal_Model_Payflow_Request
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = Mage::getModel('paypal/payflow_request');
        }

        return $this->_response;
    }

    /**
     * Fill response with data.
     *
     * @param array $postData
     * @return Mage_Paypal_Model_Payflowlink
     */
    public function setResponseData(array $postData)
    {
        foreach ($postData as $key => $val) {
            $this->getResponse()->setData(strtolower($key), $val);
        }
        return $this;
    }

    /**
     * Operate with order using data from $_POST which came from Silent Post Url.
     *
     * @param array $responseData
     * @throws Mage_Core_Exception in case of validation error or order creation error
     */
    public function process($responseData)
    {
        $debugData = array(
            'response' => $responseData
        );
        $this->_debug($debugData);

        $this->setResponseData($responseData);

        $document = $this->_getDocumentFromResponse();
        if ($document) {
            $this->_process($document);
        }
    }

    /**
     * Operate with order or quote using information from silent post
     *
     * @param Varien_Object $document
     */
    protected function _process(Varien_Object $document)
    {
        $response = $this->getResponse();
        $payment = $document->getPayment();

        if ($response->getResult() == self::RESPONSE_CODE_FRAUDSERVICE_FILTER ||
            $response->getResult() == self::RESPONSE_CODE_DECLINED_BY_FILTER
        ) {
            $fraudMessage = $this->_getFraudMessage() ? $response->getFraudMessage() : $response->getRespmsg();
            $payment->setAdditionalInformation('paypal_fraud_filters', $fraudMessage);
        }

        if ($response->getAvsdata() && strstr(substr($response->getAvsdata(), 0, 2), 'N')) {
            $payment->setAdditionalInformation('paypal_avs_code', substr($response->getAvsdata(), 0, 2));
        }

        if ($response->getCvv2match() && $response->getCvv2match() != 'Y') {
            $payment->setAdditionalInformation('paypal_cvv2_match', $response->getCvv2match());
        }

        $payment->setAdditionalInformation('authorization_id', $response->getPnref());

        /** @var $transaction Mage_Paypal_Model_Payment_Transaction */
        $transaction =  Mage::getModel('paypal/payment_transaction');
        $transaction->setTxnId($response->getPnref());

        $transaction->setAdditionalInformation('amt', $response->getAmt());

        $document->setIsChanged(1);
        $document->save();
        $transaction->save();
    }

    /**
     * Get fraud message from response
     *
     * @return string|bool
     */
    protected function _getFraudMessage()
    {
        if ($this->getResponse()->getFpsPrexmldata()) {
            $xml = new SimpleXMLElement($this->getResponse()->getFpsPrexmldata());
            $this->getResponse()->setFraudMessage((string) $xml->rule->triggeredMessage);
            return $this->getResponse()->getFraudMessage();
        }

        return false;
    }

    /**
     * Check response from Payflow gateway.
     *
     * @return Mage_Sales_Model_Abstract in case of validation passed
     * @throws Mage_Core_Exception in other cases
     */
    protected function _getDocumentFromResponse()
    {
        $response = $this->getResponse();

        $salesDocument = Mage::getModel('sales/quote')->load($response->getPonum());
        $salesDocument->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK);

        if ($this->_getSecureSilentPostHash($salesDocument->getPayment()) != $response->getUser2()
            || $this->_code != $salesDocument->getPayment()->getMethodInstance()->getCode()) {
            return false;
        }

        if ($response->getResult() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER &&
            $response->getResult() != self::RESPONSE_CODE_DECLINED_BY_FILTER &&
            $response->getResult() != self::RESPONSE_CODE_APPROVED
        ) {
            Mage::throwException($response->getRespmsg());
        }

        $fetchData = $this->fetchTransactionInfo($salesDocument->getPayment(), $response->getPnref());
        if (!isset($fetchData['custref']) || $fetchData['custref'] != $salesDocument->getReservedOrderId()) {
            Mage::throwException($this->_formatStr(self::RESPONSE_ERROR_MSG, 'Transaction error'));
        }

        return $salesDocument;
    }

    /**
     * Build request for getting token
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Varien_Object
     */
    protected function _buildTokenRequest(Varien_Object $payment)
    {
        $orderId = null;
        $amount = 0;

        $salesDocument = $payment->getOrder();
        if (!$salesDocument) {
            $salesDocument = $payment->getQuote();
            if (!$salesDocument->getReservedOrderId()) {
                $salesDocument->reserveOrderId();
            }
            $orderId = $salesDocument->getReservedOrderId();
            $amount = $salesDocument->getBaseGrandTotal();
        } else {
            $orderId = $salesDocument->getIncrementId();
            $amount = $salesDocument->getBaseTotalDue();
        }

        $request = $this->_buildBasicRequest($payment);
        if (empty($salesDocument)) {
            return $request;
        }

        $request->setCreatesecuretoken('Y')
            ->setSecuretokenid($this->_generateSecureTokenId())
            ->setTrxtype($this->_getTrxTokenType())
            ->setAmt($this->_formatStr('%.2F', $amount))
            ->setCurrency($salesDocument->getBaseCurrencyCode())
            ->setInvnum($orderId)
            ->setCustref($orderId)
            ->setPonum($salesDocument->getId())
            ->setSubtotal($salesDocument->getBaseSubtotal())
            ->setTaxamt($this->_formatStr('%.2F', $salesDocument->getBaseTaxAmount()))
            ->setFreightamt($this->_formatStr('%.2F', $salesDocument->getBaseShippingAmount()));

        $billing = $salesDocument->getBillingAddress();
        if (!empty($billing)) {
            $request->setFirstname($billing->getFirstname())
                ->setLastname($billing->getLastname())
                ->setStreet(implode(' ', $billing->getStreet()))
                ->setCity($billing->getCity())
                ->setState($billing->getRegionCode())
                ->setZip($billing->getPostcode())
                ->setCountry($billing->getCountry())
                ->setEmail($salesDocument->getCustomerEmail());
        }
        $shipping = $salesDocument->getShippingAddress();
        if (!empty($shipping)) {
            $this->_applyCountryWorkarounds($shipping);
            $request->setShiptofirstname($shipping->getFirstname())
                ->setShiptolastname($shipping->getLastname())
                ->setShiptostreet(implode(' ', $shipping->getStreet()))
                ->setShiptocity($shipping->getCity())
                ->setShiptostate($shipping->getRegionCode())
                ->setShiptozip($shipping->getPostcode())
                ->setShiptocountry($shipping->getCountry());
        }
        //pass store Id to request
        $request->setUser1($salesDocument->getStoreId())
            ->setUser2($this->_getSecureSilentPostHash($payment));

        return $request;
    }

    /**
     * Get store id from response if exists
     * or default
     *
     * @return int
     */
    protected function _getStoreId()
    {
        $response = $this->getResponse();
        if ($response->getUser1()) {
            return (int) $response->getUser1();
        }

        return Mage::app()->getStore($this->getStore())->getId();
    }

    /**
      * Return request object with basic information for gateway request
      *
      * @param Varien_Object $payment
      * @return Mage_Paypal_Model_Payflow_Request
      */
    protected function _buildBasicRequest(Varien_Object $payment)
    {
        $request = Mage::getModel('paypal/payflow_request');
        $request
            ->setUser($this->getConfigData('user', $this->_getStoreId()))
            ->setVendor($this->getConfigData('vendor', $this->_getStoreId()))
            ->setPartner($this->getConfigData('partner', $this->_getStoreId()))
            ->setPwd($this->getConfigData('pwd', $this->_getStoreId()))
            ->setVerbosity($this->getConfigData('verbosity', $this->_getStoreId()))
            ->setTender(self::TENDER_CC);
        return $request;
    }

    /**
      * Get payment action code
      *
      * @return string
      */
    protected function _getTrxTokenType()
    {
        return self::TRXTYPE_AUTH_ONLY;
    }

    /**
      * Return unique value for secure token id
      *
      * @return string
      */
    protected function _generateSecureTokenId()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * Format values
     *
     * @param mixed $format
     * @param mixed $string
     * @return mixed
     */
    protected function _formatStr($format, $string)
    {
        return sprintf($format, $string);
    }

    /**
      * If response is failed throw exception
      * Set token data in payment object
      *
      * @param Varien_Object $response
      * @param Mage_Sales_Model_Order_Payment $payment
      * @throws Mage_Core_Exception
      */
    protected function _processTokenErrors($response, $payment)
    {
        if (!$response->getSecuretoken() &&
            $response->getResult() != self::RESPONSE_CODE_APPROVED
            && $response->getResult() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER) {
            Mage::throwException($response->getRespmsg());
        } else {
            $payment->setAdditionalInformation('secure_token_id', $response->getSecuretokenid())
                ->setAdditionalInformation('secure_token', $response->getSecuretoken());
        }
    }

    /**
     * Return secure hash value for silent post request
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return string
     */
    protected function _getSecureSilentPostHash($payment)
    {
        return $payment->getAdditionalInformation($this->_secureSilentPostHashKey);
    }

    /**
     * Generate end return new secure hash value
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return string
     */
    protected function _generateSecureSilentPostHash($payment)
    {
        $secureHash = md5(Mage::helper('core')->getRandomString(10));
        $payment->setAdditionalInformation($this->_secureSilentPostHashKey, $secureHash);
        return $secureHash;
    }
}
