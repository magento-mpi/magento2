<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Link payment gateway model
 */
class Magento_Paypal_Model_Payflowlink extends Magento_Paypal_Model_Payflowpro
{
    /**
     * Default layout template
     */
    const LAYOUT_TEMPLATE = 'minLayout';

    /**
     * Controller for callback urls
     *
     * @var string
     */
    protected $_callbackController = 'payflow';

    /**
     * Response params mappings
     *
     * @var array
     */
    protected $_responseParamsMappings = array(
        'firstname' => 'billtofirstname',
        'lastname' => 'billtolastname',
        'address' => 'billtostreet',
        'city' => 'billtocity',
        'state' => 'billtostate',
        'zip' => 'billtozip',
        'country' => 'billtocountry',
        'phone' => 'billtophone',
        'email' => 'billtoemail',
        'nametoship' => 'shiptofirstname',
        'addresstoship' => 'shiptostreet',
        'citytoship' => 'shiptocity',
        'statetoship' => 'shiptostate',
        'ziptoship' => 'shiptozip',
        'countrytoship' => 'shiptocountry',
        'phonetoship' => 'shiptophone',
        'emailtoship' => 'shiptoemail',
        'faxtoship' => 'shiptofax',
        'method' => 'tender',
        'cscmatch' => 'cvv2match',
        'type' => 'trxtype',
    );

    /**
     * Payment method code
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_PAYFLOWLINK;

    /**
     * @var string
     */
    protected $_formBlockType = 'Magento_Paypal_Block_Payflow_Link_Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento_Paypal_Block_Payflow_Link_Info';

    /**#@+
     * Availability options
     */
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = true;
    /**#@-*/

    /**
     * Request & response model
     *
     * @var Magento_Paypal_Model_Payflow_Request
     */
    protected $_response;

    /**
     * Gateway request URL
     */
    const TRANSACTION_PAYFLOW_URL = 'https://payflowlink.paypal.com/';

    /**
     * Error message
     */
    const RESPONSE_ERROR_MSG = 'Payment error. %s was not found.';

    /**
     * Key for storing secure hash in additional information of payment model
     *
     * @var string
     */
    protected $_secureSilentPostHashKey = 'secure_silent_post_hash';

    /**
     * @var Magento_Paypal_Model_Payflow_RequestFactory
     */
    protected $_requestFactory;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Core_Model_WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Paypal_Model_ConfigFactory $configFactory
     * @param Magento_Paypal_Model_Payflow_RequestFactory $requestFactory
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Core_Controller_Request_Http $requestHttp
     * @param Magento_Core_Model_WebsiteFactory $websiteFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Paypal_Model_ConfigFactory $configFactory,
        Magento_Paypal_Model_Payflow_RequestFactory $requestFactory,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Core_Controller_Request_Http $requestHttp,
        Magento_Core_Model_WebsiteFactory $websiteFactory,
        array $data = array()
    ) {
        $this->_requestFactory = $requestFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_orderFactory = $orderFactory;
        $this->_requestHttp = $requestHttp;
        $this->_websiteFactory = $websiteFactory;
        parent::__construct(
            $eventManager,
            $coreData,
            $moduleList,
            $coreStoreConfig,
            $paymentData,
            $logger,
            $storeManager,
            $configFactory,
            $data
        );
    }

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
     * @param Magento_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = $this->_storeManager->getStore($this->getStore())->getId();
        /** @var Magento_Paypal_Model_Config $config */
        $config = $this->_configFactory->create()->setStoreId($storeId);
        if (Magento_Payment_Model_Method_Abstract::isAvailable($quote)
            && $config->isMethodAvailable($this->getCode())
        ) {
            return true;
        }
        return false;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Magento_Object $stateObject
     * @return \Magento_Payment_Model_Abstract|void
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction) {
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
                $this->_generateSecureSilentPostHash($payment);
                $request = $this->_buildTokenRequest($payment);
                $response = $this->_postRequest($request);
                $this->_processTokenErrors($response, $payment);

                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);

                $stateObject->setState(Magento_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
    }

    /**
     * Return response model.
     *
     * @return Magento_Paypal_Model_Payflow_Request
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = $this->_requestFactory->create();
        }

        return $this->_response;
    }

    /**
     * Fill response with data.
     *
     * @param array $postData
     * @return Magento_Paypal_Model_Payflowlink
     */
    public function setResponseData(array $postData)
    {
        foreach ($postData as $key => $val) {
            $this->getResponse()->setData(strtolower($key), $val);
        }
        foreach ($this->_responseParamsMappings as $originKey => $key) {
            $data = $this->getResponse()->getData($key);
            if (isset($data)) {
                $this->getResponse()->setData($originKey, $data);
            }
        }
        // process AVS data separately
        $avsAddr = $this->getResponse()->getData('avsaddr');
        $avsZip = $this->getResponse()->getData('avszip');
        if (isset($avsAddr) && isset($avsZip)) {
            $this->getResponse()->setData('avsdata', $avsAddr . $avsZip);
        }
        // process Name separately
        $firstnameParameter = $this->getResponse()->getData('billtofirstname');
        $lastnameParameter = $this->getResponse()->getData('billtolastname');
        if (isset($firstnameParameter) && isset($lastnameParameter)) {
            $this->getResponse()->setData('name', $firstnameParameter . ' ' . $lastnameParameter);
        }
        return $this;
    }

    /**
     * Operate with order using data from $_POST which came from Silent Post Url.
     *
     * @param array $responseData
     * @throws Magento_Core_Exception in case of validation error or order creation error
     */
    public function process($responseData)
    {
        $debugData = array(
            'response' => $responseData
        );
        $this->_debug($debugData);

        $this->setResponseData($responseData);
        $order = $this->_getOrderFromResponse();
        if ($order) {
            $this->_processOrder($order);
        }
    }

    /**
     * Operate with order using information from silent post
     *
     * @param Magento_Sales_Model_Order $order
     * @throws Magento_Core_Exception
     */
    protected function _processOrder(Magento_Sales_Model_Order $order)
    {
        $response = $this->getResponse();
        $payment = $order->getPayment();
        $payment->setTransactionId($response->getPnref())
            ->setIsTransactionClosed(0);
        $canSendNewOrderEmail = true;

        if ($response->getResult() == self::RESPONSE_CODE_FRAUDSERVICE_FILTER ||
            $response->getResult() == self::RESPONSE_CODE_DECLINED_BY_FILTER
        ) {
            $canSendNewOrderEmail = false;
            $fraudMessage = $this->_getFraudMessage() ?
                $response->getFraudMessage() : $response->getRespmsg();
            $payment->setIsTransactionPending(true)
                ->setIsFraudDetected(true)
                ->setAdditionalInformation('paypal_fraud_filters', $fraudMessage);
        }

        if ($response->getAvsdata() && strstr(substr($response->getAvsdata(), 0, 2), 'N')) {
            $payment->setAdditionalInformation('paypal_avs_code', substr($response->getAvsdata(), 0, 2));
        }
        if ($response->getCvv2match() && $response->getCvv2match() != 'Y') {
            $payment->setAdditionalInformation('paypal_cvv2_match', $response->getCvv2match());
        }

        switch ($response->getType()){
            case self::TRXTYPE_AUTH_ONLY:
                $payment->registerAuthorizationNotification($payment->getBaseAmountAuthorized());
                break;
            case self::TRXTYPE_SALE:
                $payment->registerCaptureNotification($payment->getBaseAmountAuthorized());
                break;
            default:
                break;
        }
        $order->save();

        try {
            if ($canSendNewOrderEmail) {
                $order->sendNewOrderEmail();
            }
            $this->_quoteFactory->create()
                ->load($order->getQuoteId())
                ->setIsActive(false)
                ->save();
        } catch (Exception $e) {
            throw new Magento_Core_Exception(__('We cannot send the new order email.'));
        }
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
     * @return Magento_Sales_Model_Order in case of validation passed
     * @throws Magento_Core_Exception in other cases
     */
    protected function _getOrderFromResponse()
    {
        $response = $this->getResponse();
        $order = $this->_orderFactory->create()->loadByIncrementId($response->getInvnum());

        if ($this->_getSecureSilentPostHash($order->getPayment()) != $response->getUser2()
            || $this->_code != $order->getPayment()->getMethodInstance()->getCode()
        ) {
            return false;
        }

        if ($response->getResult() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER
            && $response->getResult() != self::RESPONSE_CODE_DECLINED_BY_FILTER
            && $response->getResult() != self::RESPONSE_CODE_APPROVED
        ) {
            if ($order->getState() != Magento_Sales_Model_Order::STATE_CANCELED) {
                $order->registerCancellation($response->getRespmsg())->save();
            }
            throw new Magento_Core_Exception($response->getRespmsg());
        }

        $amountCompared = ($response->getAmt() == $order->getPayment()->getBaseAmountAuthorized()) ? true : false;
        if (!$order->getId()
            || $order->getState() != Magento_Sales_Model_Order::STATE_PENDING_PAYMENT
            || !$amountCompared
        ) {
            throw new Magento_Core_Exception($this->_formatStr(self::RESPONSE_ERROR_MSG, 'Order'));
        }

        $fetchData = $this->fetchTransactionInfo($order->getPayment(), $response->getPnref());
        if (!isset($fetchData['custref']) || $fetchData['custref'] != $order->getIncrementId()) {
            throw new Magento_Core_Exception($this->_formatStr(self::RESPONSE_ERROR_MSG, 'Transaction'));
        }

        return $order;
    }

    /**
     * Build request for getting token
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Object
     */
    protected function _buildTokenRequest(Magento_Sales_Model_Order_Payment $payment)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setCreatesecuretoken('Y')
            ->setSecuretokenid($this->_generateSecureTokenId())
            ->setTrxtype($this->_getTrxTokenType())
            ->setAmt($this->_formatStr('%.2F', $payment->getOrder()->getBaseTotalDue()))
            ->setCurrency($payment->getOrder()->getBaseCurrencyCode())
            ->setInvnum($payment->getOrder()->getIncrementId())
            ->setCustref($payment->getOrder()->getIncrementId())
            ->setPonum($payment->getOrder()->getId());
        //This is PaPal issue with taxes and shipping
            //->setSubtotal($this->_formatStr('%.2F', $payment->getOrder()->getBaseSubtotal()))
            //->setTaxamt($this->_formatStr('%.2F', $payment->getOrder()->getBaseTaxAmount()))
            //->setFreightamt($this->_formatStr('%.2F', $payment->getOrder()->getBaseShippingAmount()));


        $order = $payment->getOrder();
        if (empty($order)) {
            return $request;
        }

        $billing = $order->getBillingAddress();
        if (!empty($billing)) {
            $request->setFirstname($billing->getFirstname())
                ->setLastname($billing->getLastname())
                ->setStreet(implode(' ', $billing->getStreet()))
                ->setCity($billing->getCity())
                ->setState($billing->getRegionCode())
                ->setZip($billing->getPostcode())
                ->setCountry($billing->getCountry())
                ->setEmail($order->getCustomerEmail());
        }
        $shipping = $order->getShippingAddress();
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
        $request->setUser1($order->getStoreId())
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
            return (int)$response->getUser1();
        }
        return $this->_storeManager->getStore($this->getStore())->getId();
    }

    /**
     * Return request object with basic information for gateway request
     *
     * @param Magento_Object $payment
     * @return Magento_Paypal_Model_Payflow_Request
     */
    protected function _buildBasicRequest(Magento_Object $payment)
    {
        /** @var Magento_Paypal_Model_Payflow_Request $request */
        $request = $this->_requestFactory->create();
        $cscEditable = $this->getConfigData('csc_editable');
        $request
            ->setUser($this->getConfigData('user', $this->_getStoreId()))
            ->setVendor($this->getConfigData('vendor', $this->_getStoreId()))
            ->setPartner($this->getConfigData('partner', $this->_getStoreId()))
            ->setPwd($this->getConfigData('pwd', $this->_getStoreId()))
            ->setVerbosity($this->getConfigData('verbosity', $this->_getStoreId()))
            ->setData('BNCODE', $this->getConfigData('bncode'))
            ->setTender(self::TENDER_CC)
            ->setCancelurl($this->_getCallbackUrl('cancelPayment'))
            ->setErrorurl($this->_getCallbackUrl('returnUrl'))
            ->setSilentpost('TRUE')
            ->setSilentposturl($this->_getCallbackUrl('silentPost'))
            ->setReturnurl($this->_getCallbackUrl('returnUrl'))
            ->setTemplate(self::LAYOUT_TEMPLATE)
            ->setDisablereceipt('TRUE')
            ->setCscrequired($cscEditable && $this->getConfigData('csc_required') ? 'TRUE' : 'FALSE')
            ->setCscedit($cscEditable ? 'TRUE' : 'FALSE')
            ->setEmailcustomer($this->getConfigData('email_confirmation') ? 'TRUE' : 'FALSE')
            ->setUrlmethod($this->getConfigData('url_method'));
        return $request;
    }

    /**
     * Get payment action code
     *
     * @return string
     */
    protected function _getTrxTokenType()
    {
        switch ($this->getConfigData('payment_action')) {
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
                return self::TRXTYPE_AUTH_ONLY;
            case Magento_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                return self::TRXTYPE_SALE;
            default:
                break;
        }
    }

    /**
     * Return unique value for secure token id
     *
     * @return string
     */
    protected function _generateSecureTokenId()
    {
        return $this->_coreData->uniqHash();
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
     * @param Magento_Object $response
     * @param Magento_Sales_Model_Order_Payment $payment
     * @throws Magento_Core_Exception
     */
    protected function _processTokenErrors($response, $payment)
    {
        if (!$response->getSecuretoken()
            && $response->getResult() != self::RESPONSE_CODE_APPROVED
            && $response->getResult() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER
        ) {
            throw new Magento_Core_Exception($response->getRespmsg());
        } else {
            $payment->setAdditionalInformation('secure_token_id', $response->getSecuretokenid())
                ->setAdditionalInformation('secure_token', $response->getSecuretoken());
        }
    }

    /**
     * Return secure hash value for silent post request
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return string
     */
    protected function _getSecureSilentPostHash($payment)
    {
        return $payment->getAdditionalInformation($this->_secureSilentPostHashKey);
    }

    /**
     * Generate end return new secure hash value
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return string
     */
    protected function _generateSecureSilentPostHash($payment)
    {
        $secureHash = md5($this->_coreData->getRandomString(10));
        $payment->setAdditionalInformation($this->_secureSilentPostHashKey, $secureHash);
        return $secureHash;
    }

    /**
     * Add transaction with correct transaction Id
     *
     * @deprecated since 1.6.2.0
     * @param Magento_Object $payment
     * @param string $txnId
     */
    protected function _addTransaction($payment, $txnId)
    {
    }

    /**
     * Initialize request
     *
     * @deprecated since 1.6.2.0
     * @param Magento_Object $payment
     * @param  $amount
     * @return Magento_Paypal_Model_Payflowlink
     */
    protected function _initialize(Magento_Object $payment, $amount)
    {
        return $this;
    }

    /**
     * Check whether order review has enough data to initialize
     *
     * @deprecated since 1.6.2.0
     * @param $token
     * @throws Magento_Core_Exception
     */
    public function prepareOrderReview($token = null)
    {
    }

    /**
     * Additional authorization logic for Account Verification
     *
     * @deprecated since 1.6.2.0
     * @param Magento_Object $payment
     * @param mixed $amount
     * @param Magento_Paypal_Model_Payment_Transaction $transaction
     * @param string $txnId
     * @return Magento_Paypal_Model_Payflowlink
     */
    protected function _authorize(Magento_Object $payment, $amount, $transaction, $txnId)
    {
        return $this;
    }

    /**
     * Operate with order or quote using information from silent post
     *
     * @deprecated since 1.6.2.0
     * @param Magento_Object $document
     */
    protected function _process(Magento_Object $document)
    {
    }

    /**
     * Check Transaction
     *
     * @deprecated since 1.6.2.0
     * @param Magento_Paypal_Model_Payment_Transaction $transaction
     * @param mixed $amount
     * @return Magento_Paypal_Model_Payflowlink
     */
    protected function _checkTransaction($transaction, $amount)
    {
        return $this;
    }

    /**
     * Check response from Payflow gateway.
     *
     * @deprecated since 1.6.2.0
     * @return Magento_Sales_Model_Abstract in case of validation passed
     * @throws Magento_Core_Exception in other cases
     */
    protected function _getDocumentFromResponse()
    {
        return null;
    }


    /**
     * Get callback controller
     *
     * @return string
     */
    public function getCallbackController()
    {
        return $this->_callbackController;
    }

    /**
     * Get callback url
     *
     * @param string $actionName
     * @return string
     */
    protected function _getCallbackUrl($actionName)
    {
        if ($this->_requestHttp->getParam('website')) {
            /** @var $website Magento_Core_Model_Website */
            $website = $this->_websiteFactory->create()->load($this->_requestHttp->getParam('website'));
            $secure = $this->_coreStoreConfig->getConfigFlag(
                Magento_Core_Model_Url::XML_PATH_SECURE_IN_FRONT,
                $website->getDefaultStore()
            );
            $path = $secure
                ? Magento_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL
                : Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL;
            $websiteUrl = $this->_coreStoreConfig->getConfig($path, $website->getDefaultStore());
        } else {
            $secure = $this->_coreStoreConfig->getConfigFlag(Magento_Core_Model_Url::XML_PATH_SECURE_IN_FRONT);
            $websiteUrl = $this->_storeManager->getStore()
                ->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_LINK, $secure);
        }

        return $websiteUrl . 'paypal/' . $this->getCallbackController() . '/' . $actionName;
    }
}
