<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * Suppress the rules as the class extends Mage_Payment_Model_Method_Abstract which is not refactored
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class Mage_Cardgate_Model_Gateway_Abstract extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Checkout Session
     *
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Sales Order factory
     *
     * @var Mage_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * URL generator
     *
     * @var Magento_Core_Model_Url
     */
    protected $_urlGenerator;

    /**
     * Card Gate Base Object
     *
     * @var Mage_Cardgate_Model_Base
     */
    protected $_base;

    /**
     * Store Config object
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Helper object
     *
     * @var Mage_Cardgate_Helper_Data
     */
    protected $_helper;

    /**
     * Cardgate Form Block class name
     *
     * @var string
     */
    protected $_formBlockType = 'Mage_Cardgate_Block_Form';

    /**
     * Cardgate Payment Method Code
     *
     * @var string
     */
    protected $_code;

    /**
     * Cardgate Payment Model Code
     *
     * @var string
     */
    protected $_model;

    /**
     * CardGatePlus features
     *
     * @var mixed
     */
    const CARDGATE_URL = 'https://gateway.cardgateplus.com/';

    /**
     * Codes of supported currencies
     *
     * @var array
     */
    protected $_supportedCurrencies = array(
        'EUR', 'USD', 'JPY', 'BGN', 'CZK',
        'DKK', 'GBP', 'HUF', 'LTL', 'LVL',
        'PLN', 'RON', 'SEK', 'CHF', 'NOK',
        'HRK', 'RUB', 'TRY', 'AUD', 'BRL',
        'CAD', 'CNY', 'HKD', 'IDR', 'ILS',
        'INR', 'KRW', 'MXN', 'MYR', 'NZD',
        'PHP', 'SGD', 'THB', 'ZAR',
    );

    /**
     * Mage_Payment_Model settings
     *
     * @var bool
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    /**
     * Constructor
     *
     * @param Mage_Checkout_Model_Session $checkoutSession
     * @param Mage_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Core_Model_Url $urlGenerator
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Mage_Cardgate_Model_Base $base
     * @param Mage_Cardgate_Helper_Data $helper
     */
    public function __construct(
        Mage_Checkout_Model_Session $checkoutSession,
        Mage_Sales_Model_OrderFactory $orderFactory,
        Magento_Core_Model_Url $urlGenerator,
        Magento_Core_Model_Store_Config $storeConfig,
        Mage_Cardgate_Model_Base $base,
        Mage_Cardgate_Helper_Data $helper
    ) {
        parent::__construct();

        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_urlGenerator = $urlGenerator;
        $this->_storeConfig = $storeConfig;
        $this->_base = $base;
        $this->_helper = $helper;
    }

    /**
     * Return Gateway Url
     *
     * @return string
     */
    public function getGatewayUrl()
    {
        return self::CARDGATE_URL;
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Get current order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
        return $order;
    }

    /**
     * Magento tries to set the order from payment/, instead of cardgate/
     *
     * @param Mage_Sales_Model_Order $order
     * @return void
     *
     * Suppress this rule as $order parameter is a part of method signature
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSortOrder($order)
    {
        parent::setSortOrder($this->getConfigData('sort_order'));
    }

    /**
     * Append the current model to the URL
     *
     * @param string $url
     * @return string
     */
    public function getModelUrl($url)
    {
        $params = array('_secure' => true);
        if (!empty($this->_model)) {
            $params['model'] = $this->_model;
        }
        return $this->_urlGenerator->getUrl($url, $params);
    }

    /**
     * Magento will use this for payment redirection
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getModelUrl('cardgate/cardgate/redirect');
    }

    /**
     * Retrieve config value for store by path
     *
     * @param string $field
     * @param int|string|null|Magento_Core_Model_Store $storeId
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        $value = parent::getConfigData($field, $storeId);

        if ($field != 'active' && !$value) {
            if (null === $storeId) {
                $storeId = $this->getStore();
            }
            $path = 'payment/cardgate/' . $field;
            $value = $this->_storeConfig->getConfig($path, $storeId);
        }

        return $value;
    }

    /**
     * Validate if the currency code is supported by CardGatePlus
     *
     * @return Mage_Cardgate_Model_Gateway_Abstract
     */
    public function validate()
    {
        parent::validate();

        $currencyCode = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currencyCode, $this->_supportedCurrencies)) {
            $this->_base->log('Unacceptable currency code (' . $currencyCode . ').');
            Mage::throwException(
                $this->_helper->__('Selected currency code ') . $currencyCode .
                    $this->_helper->__(' is not compatible with CardGatePlus'));
        }

        return $this;
    }

    /**
     * Generates checkout form fields
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getCheckoutFormFields()
    {
        $order = $this->getOrder();
        $customer = $order->getBillingAddress();
        // Change order status
        $newState = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $newStatus = $this->getConfigData('initialized_status');
        $statusMessage = $this->_helper->__('Transaction started, waiting for payment.');
        $order->setState($newState, $newStatus, $statusMessage);
        $order->save();

        $fields = array();
        switch ($this->_model) {
            // Credit cards
            case 'creditcard':
                $fields['option'] = 'creditcard';
                break;

            // DIRECTebanking
            case 'sofortbanking':
                $fields['option'] = 'directebanking';
                break;

            // iDEAL
            case 'ideal':
                $fields['option'] = 'ideal';
                $fields['suboption'] = $order->getPayment()->getAdditionalInformation('ideal_issuer_id');
                break;

            // Mister Cash
            case 'mistercash':
                $fields['option'] = 'mistercash';
                break;

            // Default
            default:
                $fields['option'] = '';
                $fields['suboption'] = '';
                break;
        }

        $currencyCode = $order->getBaseCurrencyCode();

        $orderId = $order->getIncrementId();
        $orderId = $this->getConfigData('transaction_id_prefix')
            ? $this->getConfigData('transaction_id_prefix') . '-' . $orderId
            : $orderId;

        $fields['siteid'] = $this->getConfigData('site_id');
        $fields['ref'] = $orderId;
        $fields['first_name'] = $customer->getFirstname();
        $fields['last_name'] = $customer->getLastname();
        $fields['email'] = $order->getCustomerEmail();
        $fields['address'] = $customer->getStreet(1) .
            ($customer->getStreet(2) ? ', ' . $customer->getStreet(2) : '');
        $fields['city'] = $customer->getCity();
        $fields['country_code'] = $customer->getCountry();
        $fields['postal_code'] = $customer->getPostcode();
        $fields['phone_number'] = $customer->getTelephone();
        $fields['state'] = $customer->getRegionCode();
        $fields['language']  = $this->getConfigData('lang');
        $fields['return_url'] = $this->_urlGenerator->getUrl('cardgate/cardgate/success/', array('_secure' => true));
        $fields['return_url_failed'] =
            $this->_urlGenerator->getUrl('cardgate/cardgate/cancel/', array('_secure' => true));
        $fields['shop_version'] = 'Magento ' . Mage::getVersion();

        if ($this->_base->isTest()) {
            $fields['test'] = '1';
            $hashPrefix = 'TEST';
        } else {
            $hashPrefix = '';
        }

        $fields['currency'] = $currencyCode;
        $fields['amount'] = sprintf('%.0f', $order->getBaseTotalDue() * 100);
        $fields['description'] = str_replace('%id%',
            $orderId,
            $this->getConfigData('order_description'));
        $fields['hash'] = md5($hashPrefix .
            $this->getConfigData('site_id') .
            $fields['amount'] .
            $fields['ref'] .
            $this->getConfigData('hash_key'));

        // Logging
        $this->_base->log('Initiating a new transaction');
        $this->_base->log('Sending customer to CardGatePlus with values:');
        $this->_base->log('URL = ' . $this->getGatewayUrl());
        $this->_base->log($fields);

        return $fields;
    }
}
