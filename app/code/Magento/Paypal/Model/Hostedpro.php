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
 * Website Payments Pro Hosted Solution payment gateway model
 */
class Magento_Paypal_Model_Hostedpro extends Magento_Paypal_Model_Direct
{
    /**
     * Button code
     */
    const BM_BUTTON_CODE    = 'TOKEN';

    /**
     * Button type
     */
    const BM_BUTTON_TYPE    = 'PAYMENT';

    /**
     * Paypal API method name for button creation
     */
    const BM_BUTTON_METHOD  = 'BMCreateButton';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_HOSTEDPRO;

    /**
     * @var string
     */
    protected $_formBlockType = 'Magento_Paypal_Block_Hosted_Pro_Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento_Paypal_Block_Hosted_Pro_Info';

    /**#@+
     * Availability options
     */
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    protected $_isInitializeNeeded      = true;
    /**#@-*/

    /**
     * @var Magento_Paypal_Model_Hostedpro_RequestFactory
     */
    protected $_hostedproRequestFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Centinel_Model_Service $centinelService
     * @param Magento_Paypal_Model_Method_ProTypeFactory $proTypeFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Controller_Request_Http $requestHttp
     * @param Magento_Paypal_Model_CartFactory $cartFactory
     * @param Magento_Paypal_Model_Hostedpro_RequestFactory $hostedproRequestFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Centinel_Model_Service $centinelService,
        Magento_Paypal_Model_Method_ProTypeFactory $proTypeFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Controller_Request_Http $requestHttp,
        Magento_Paypal_Model_CartFactory $cartFactory,
        Magento_Paypal_Model_Hostedpro_RequestFactory $hostedproRequestFactory,
        array $data = array()
    ) {
        $this->_hostedproRequestFactory = $hostedproRequestFactory;
        parent::__construct(
            $logger,
            $eventManager,
            $coreStoreConfig,
            $moduleList,
            $paymentData,
            $logAdapterFactory,
            $locale,
            $centinelService,
            $proTypeFactory,
            $storeManager,
            $urlBuilder,
            $requestHttp,
            $cartFactory,
            $data
        );
    }

    /**
     * Return available CC types for gateway based on merchant country.
     * We do not have to check the availability of card types.
     *
     * @return bool
     */
    public function getAllowedCcTypes()
    {
        return true;
    }

    /**
     * Return merchant country code from config,
     * use default country if it not specified in General settings
     *
     * @return string
     */
    public function getMerchantCountry()
    {
        return $this->_pro->getConfig()->getMerchantCountry();
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
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Magento_Object $stateObject
     * @return \Magento_Payment_Model_Abstract|null
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

                $this->_setPaymentFormUrl($payment);

                $stateObject->setState(Magento_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
    }

    /**
     * Sends API request to PayPal to get form URL, then sets this URL to $payment object.
     *
     * @param Magento_Payment_Model_Info $payment
     * @throws Magento_Core_Exception
     */
    protected function _setPaymentFormUrl(Magento_Payment_Model_Info $payment)
    {
        $request = $this->_buildFormUrlRequest($payment);
        $response = $this->_sendFormUrlRequest($request);
        if ($response) {
            $payment->setAdditionalInformation('secure_form_url', $response);
        } else {
            throw new Magento_Core_Exception('Cannot get secure form URL from PayPal');
        }
    }

    /**
     * Returns request object with needed data for API request to PayPal to get form URL.
     *
     * @param Magento_Payment_Model_Info $payment
     * @return Magento_Paypal_Model_Hostedpro_Request
     */
    protected function _buildFormUrlRequest(Magento_Payment_Model_Info $payment)
    {
        $request = $this->_buildBasicRequest()
            ->setOrder($payment->getOrder())
            ->setPaymentMethod($this);

        return $request;
    }

    /**
     * Returns form URL from request to PayPal.
     *
     * @param Magento_Paypal_Model_Hostedpro_Request $request
     * @return string | false
     */
    protected function _sendFormUrlRequest(Magento_Paypal_Model_Hostedpro_Request $request)
    {
        $api = $this->_pro->getApi();
        $response = $api->call(self::BM_BUTTON_METHOD, $request->getRequestData());

        if (!isset($response['EMAILLINK'])) {
            return false;
        }
        return $response['EMAILLINK'];
    }

    /**
     * Return request object with basic information
     *
     * @return Magento_Paypal_Model_Hostedpro_Request
     */
    protected function _buildBasicRequest()
    {
        $request = $this->_hostedproRequestFactory->create()->setData(array(
            'METHOD'     => self::BM_BUTTON_METHOD,
            'BUTTONCODE' => self::BM_BUTTON_CODE,
            'BUTTONTYPE' => self::BM_BUTTON_TYPE
        ));
        return $request;
    }

    /**
     * Get return URL
     *
     * @param int $storeId
     * @return string
     */
    public function getReturnUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/return', $storeId);
    }

    /**
     * Get notify (IPN) URL
     *
     * @param int $storeId
     * @return string
     */
    public function getNotifyUrl($storeId = null)
    {
        return $this->_getUrl('paypal/ipn', $storeId, false);
    }

    /**
     * Get cancel URL
     *
     * @param int $storeId
     * @return string
     */
    public function getCancelUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/cancel', $storeId);
    }

    /**
     * Build URL for store
     *
     * @param string $path
     * @param int $storeId
     * @param bool $secure
     * @return string
     */
    protected function _getUrl($path, $storeId, $secure = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        return $this->_urlBuilder->getUrl($path, array(
            "_store"   => $store,
            "_secure"  => is_null($secure) ? $store->isCurrentlySecure() : $secure
        ));
    }
}
