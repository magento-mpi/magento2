<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Helper;

use Magento\Store\Model\Store;
use Magento\Pbridge\Model\Encryption;
use Magento\Sales\Model\Quote;

/**
 * Pbridge helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Payment Bridge action name to fetch Payment Bridge gateway form
     *
     * @var string
     */
    const PAYMENT_GATEWAY_FORM_ACTION = 'GatewayForm';

    /**
     * Payment Bridge action name to fetch Payment Bridge Saved Payment (Credit Card) profiles
     *
     * @var string
     */
    const PAYMENT_GATEWAY_PAYMENT_PROFILE_ACTION = 'ManageSavedPayment';

    /**
     * Payment Bridge payment methods available for the current merchant
     *
     * @var array
     */
    protected $_pbridgeAvailableMethods = array();

    /**
     * Payment Bridge payment methods available for the current merchant and usable for current conditions
     *
     * @var array
     */
    protected $_pbridgeUsableMethods = array();

    /**
     * Encryptor model
     *
     * @var Encryption|null
     */
    protected $_encryptor = null;

    /**
     * Store id
     *
     * @var int|null
     */
    protected $_storeId = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Encryption factory
     *
     * @var \Magento\Pbridge\Model\EncryptionFactory
     */
    protected $_encryptionFactory;

    /**
     * Application state
     * 
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Payment\Model\CartFactory
     */
    protected $_cartFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Pbridge\Model\EncryptionFactory $encryptionFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Payment\Model\CartFactory $cartFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Pbridge\Model\EncryptionFactory $encryptionFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Payment\Model\CartFactory $cartFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_encryptionFactory = $encryptionFactory;
        $this->_appState = $appState;
        $this->_cartFactory = $cartFactory;
        parent::__construct($context);
    }

    /**
     * Check if Payment Bridge Magento Module is enabled in configuration
     *
     * @param Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            'payment/pbridge/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ) && $this->isAvailable(
            $store
        );
    }

    /**
     * Check if Payment Bridge supports Payment Profiles
     *
     * @param Store|null $store
     * @return bool
     */
    public function arePaymentProfilesEnables($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            'payment/pbridge/profilestatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ) && $this->isEnabled(
            $store
        );
    }

    /**
     * Check if enough config paramters to use Pbridge module
     *
     * @param Store|int|null $store
     * @return bool
     */
    public function isAvailable($store = null)
    {
        return (bool)$this->_scopeConfig->getValue(
            'payment/pbridge/gatewayurl',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ) && (bool)$this->_scopeConfig->getValue(
            'payment/pbridge/merchantcode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ) && (bool)$this->_scopeConfig->getValue(
            'payment/pbridge/merchantkey',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Getter
     *
     * @param Quote|null $quote
     * @return Quote|null
     */
    protected function _getQuote($quote = null)
    {
        if ($quote && $quote instanceof Quote) {
            return $quote;
        }
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Generate identifier based on email or ID
     *
     * @param string $email Customer e-mail or customer ID
     * @param int $storeId
     * @return null|string
     */
    public function getCustomerIdentifierByEmail($email, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        $merchantCode = $this->_scopeConfig->getValue(
            'payment/pbridge/merchantcode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $uniqueId = $this->_scopeConfig->getValue(
            'payment/pbridge/uniquekey',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($uniqueId) {
            $uniqueId .= '@';
        }
        return md5($uniqueId . $email . '@' . $merchantCode);
    }

    /**
     * Prepare and return Payment Bridge request url with parameters if passed.
     * Encrypt parameters by default.
     *
     * @param array $params OPTIONAL
     * @param boolean $encryptParams OPTIONAL true by default
     * @return string
     */
    protected function _prepareRequestUrl($params = array(), $encryptParams = true)
    {
        $storeId = isset($params['store_id']) ? $params['store_id'] : $this->_storeId;
        $pbridgeUrl = $this->getBridgeBaseUrl($storeId);
        $sourceUrl = rtrim($pbridgeUrl, '/') . '/bridge.php';

        if (!empty($params)) {
            if ($encryptParams) {
                $params = array('data' => $this->encrypt(json_encode($params)));
            }
        }

        $params['merchant_code'] = trim(
            $this->_scopeConfig->getValue(
                'payment/pbridge/merchantcode',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );

        $sourceUrl .= '?' . http_build_query($params);

        return $sourceUrl;
    }

    /**
     * Prepare required request params.
     * Optinal accept additional params to merge with required
     *
     * @param array $params OPTIONAL
     * @return array
     */
    public function getRequestParams(array $params = array())
    {
        $params = array_merge(array('locale' => $this->_localeResolver->getLocaleCode()), $params);

        $params['merchant_key'] = trim(
            $this->_scopeConfig->getValue(
                'payment/pbridge/merchantkey',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeId
            )
        );

        $params['scope'] = $this->_appState->getAreaCode() ==
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE ? 'backend' : 'frontend';

        return $params;
    }

    /**
     * Return payment Bridge request URL to display gateway form
     *
     * @param array $params OPTIONAL
     * @param Quote|null $quote
     * @return string
     */
    public function getGatewayFormUrl(array $params = array(), $quote = null)
    {
        $quote = $this->_getQuote($quote);
        $reservedOrderId = '';
        if ($quote && $quote->getId()) {
            if (!$quote->getReservedOrderId()) {
                $quote->reserveOrderId()->save();
            }
            $reservedOrderId = $quote->getReservedOrderId();
        }
        $params = array_merge(
            array(
                'order_id' => $reservedOrderId,
                'amount' => $quote ? $quote->getBaseGrandTotal() : '0',
                'currency_code' => $quote ? $quote->getBaseCurrencyCode() : '',
                'client_identifier' => md5($quote->getId()),
                'store_id' => $quote ? $quote->getStoreId() : '0'
            ),
            $params
        );

        if ($quote->getStoreId()) {
            $this->setStoreId($quote->getStoreId());
        }

        $params = $this->getRequestParams($params);
        $params['action'] = self::PAYMENT_GATEWAY_FORM_ACTION;
        return $this->_prepareRequestUrl($params, true);
    }

    /**
     * Return Payment Bridge target URL to display Credit card profiles
     *
     * @param array $params Additional URL query params
     * @return string
     */
    public function getPaymentProfileUrl(array $params = array())
    {
        $params = $this->getRequestParams($params);
        $params['action'] = self::PAYMENT_GATEWAY_PAYMENT_PROFILE_ACTION;
        $customer = $this->_customerSession->getCustomer();
        $params['customer_name'] = $customer->getName();
        $params['customer_email'] = $customer->getEmail();
        return $this->_prepareRequestUrl($params, true);
    }

    /**
     * Getter.
     * Retrieve Payment Bridge url
     *
     * @param array $params
     * @return string
     */
    public function getRequestUrl($params = array())
    {
        return $this->_prepareRequestUrl($params);
    }

    /**
     * Return a modified encryptor
     *
     * @return Encryption
     */
    public function getEncryptor()
    {
        if ($this->_encryptor === null) {
            $key = trim(
                (string)$this->_scopeConfig->getValue(
                    'payment/pbridge/transferkey',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->_storeId
                )
            );
            $this->_encryptor = $this->_encryptionFactory->create(array('key' => $key));
        }
        return $this->_encryptor;
    }

    /**
     * Decrypt data array
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return $this->getEncryptor()->decrypt($data);
    }

    /**
     * Encrypt data array
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        return $this->getEncryptor()->encrypt($data);
    }

    /**
     * Retrieve Payment Bridge specific GET parameters
     *
     * @return array
     */
    public function getPbridgeParams()
    {
        $decryptData = $this->decrypt($this->_getRequest()->getParam('data', ''));
        $data = json_decode($decryptData, true);
        $data = array(
            'original_payment_method' => isset($data['original_payment_method'])
                    ? $data['original_payment_method']
                    : null,
            'token' => isset($data['token']) ? $data['token'] : null,
            'cc_last4' => isset($data['cc_last4']) ? $data['cc_last4'] : null,
            'cc_type' => isset($data['cc_type']) ? $data['cc_type'] : null,
            'x_params' => isset($data['x_params']) ? serialize($data['x_params']) : null
        );

        return $data;
    }

    /**
     * Prepare cart from order
     *
     * @param \Magento\Framework\Model\AbstractModel $order
     * @return array
     */
    public function prepareCart($order)
    {
        return $this->_prepareCart($this->_cartFactory->create(array('salesModel' => $order)));
    }

    /**
     * Prepare cart line items
     *
     * @param \Magento\Payment\Model\Cart $cart
     * @return array
     */
    protected function _prepareCart(\Magento\Payment\Model\Cart $cart)
    {
        $items = $cart->getAllItems();

        $result = array();
        foreach ($items as $item) {
            $result['items'][] = $item->getData();
        }

        return array_merge($result, $cart->getAmounts());
    }

    /**
     * Return base bridge URL
     *
     * @return string
     */
    public function getBridgeBaseUrl()
    {
        return trim(
            $this->_scopeConfig->getValue(
                'payment/pbridge/gatewayurl',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeId
            )
        );
    }

    /**
     * Store id setter
     *
     * @param int $storeId
     * @return void
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param string $name template name
     * @return string
     */
    public function getReviewButtonTemplate($name)
    {
        $quote = $this->_checkoutSession->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment->getMethodInstance()->getIsDeferred3dCheck()) {
                return $name;
            }
        }
        return '';
    }

    /**
     * Get template for Continue button to save order and load iframe
     *
     * @param string $name template name
     * @return string
     */
    public function getContinueButtonTemplate($name)
    {
        $quote = $this->_checkoutSession->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && $payment->getMethodInstance()->getIsPendingOrderRequired()) {
                return $name;
            }
        }
        return '';
    }
}
