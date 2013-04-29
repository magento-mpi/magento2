<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Onboarding config model
 *
 * @category   Saas
 * @package    Saas_Paypal
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Paypal_Model_Boarding_Config extends Mage_Paypal_Model_Config
{
    /**
     * PayPal Website Payments Pro - Express Checkout (onboarding)
     *
     * @var string
     */
    const METHOD_EXPRESS_BOARDING = 'paypal_express_boarding';

    /**
     * PayPal Website Payments Pro - Direct Payments (onboarding)
     *
     * @var string
     */
    const METHOD_DIRECT_BOARDING  = 'paypal_direct_boarding';

    /**
     * Product list codes for payment methods.
     * Needed for SOAP EnterBoarding request.
     *
     * @var array
     */
    protected $_productList = array(
        self::METHOD_EXPRESS_BOARDING => 'ec',
        self::METHOD_DIRECT_BOARDING => 'ec,dp'
    );

    /**
     * Permission groups
     *
     * @var array
     */
    protected $_permissionGroups = array(
        'DIRECT_PAYMENT',
        'EXPRESS_CHECKOUT',
        'SETTLEMENT_REPORTING',
        'AUTH_CAPTURE',
        'BILLING_AGREEMENT',
        'REFERENCE_TRANSACTION',
        'TRANSACTION_DETAILS',
        'TRANSACTION_SEARCH',
        'REFUND',
        'MANAGE_PENDING_TRANSACTION_STATUS',
        'ACCESS_BASIC_PERSONAL_DATA'
    );

    /**
     * MarketingCategories list for payment methods
     *
     * @var array
     */
    protected $_marketingCategories = array(
        self::METHOD_EXPRESS_BOARDING => 2,
        self::METHOD_DIRECT_BOARDING => 1
    );

    /**
     * Available PayPal methods
     *
     * @var array
     */
    protected $_availableMethods = array(
        self::METHOD_EXPRESS_BOARDING,
        self::METHOD_DIRECT_BOARDING,
        self::METHOD_BILLING_AGREEMENT,
    );

    /**
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param array $params
     */
    public function __construct(Mage_Core_Model_Config_Storage_WriterInterface $configWriter, $params = array())
    {
        $this->_configWriter = $configWriter;
        parent::__construct($params);

    }

    /**
     * Get permission groups for requested payment method
     *
     * @param string $methodCode
     * @return array
     */
    public function getPermissionGroups($methodCode)
    {
        if ($methodCode == self::METHOD_EXPRESS_BOARDING) {
            //Remove DIRECT_PAYMENT permission group in case of Express Checkout
            unset($this->_permissionGroups[0]);
        }

        return $this->_permissionGroups;
    }

    /**
     * Check whether method available for checkout or not
     * Logic based on merchant country, methods dependence
     *
     * @param null $methodCode
     * @internal param string $method Method code
     * @return bool
     */
    public function isMethodAvailable($methodCode = null)
    {
        if ($methodCode === null) {
            $methodCode = $this->getMethodCode();
        }

        $result = true;

        if (!$this->isMethodActive($methodCode) || !$this->boarding_account ||
            !in_array($methodCode, $this->_availableMethods)
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check whether method active in configuration, supported for merchant country or not
     * and boarding status
     *
     * @param string $method Method code
     * @return bool
     */
    public function isMethodActive($method)
    {
        $isStatusActive = $this->_getStoreConfig("payment/{$method}/status", $this->_storeId) ==
            Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE;
        $useAccelerated = $this->shouldUseUnilateralPayments();

        if ($this->isMethodEnabled($method)
            && ($isStatusActive || $useAccelerated || $method == self::METHOD_BILLING_AGREEMENT)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check whether method enabled in configuration and supported for merchant country
     *
     * @param string $method Method code
     * @return bool
     */
    public function isMethodEnabled($method)
    {
        $isMethodActive = $this->_getStoreConfigFlag("payment/{$method}/active", $this->_storeId);
        return $this->isMethodSupportedForCountry($method) && $isMethodActive;
    }

    /**
     * Get full needed product list string for EnterBoarding SOAP request
     *
     * @param string $methodCode
     * @return string|null
     */
    public function getProductList($methodCode)
    {
        if (isset($this->_productList[$methodCode])) {
            return $this->_productList[$methodCode] . ',auth_settle,admin_api';
        }
        return null;
    }

    /**
     * Get marketing category value for payment method.
     *
     * @param string $methodCode
     * @return int|null
     */
    public function getMarketingCategory($methodCode)
    {
        if (isset($this->_marketingCategories[$methodCode])) {
            return $this->_marketingCategories[$methodCode];
        }
        return null;
    }

    /**
     * Return list of allowed methods for specified country iso code
     *
     * @param string $countryCode 2-letters iso code
     * @return array
     */
    public function getCountryMethods($countryCode = null)
    {
        $countryMethods = parent::getCountryMethods($countryCode);
        if (in_array(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS, $countryMethods)) {
            $countryMethods[] = self::METHOD_EXPRESS_BOARDING;
        }
        if (in_array(Mage_Paypal_Model_Config::METHOD_WPP_DIRECT, $countryMethods)) {
            $countryMethods[] = self::METHOD_DIRECT_BOARDING;
        }
        return $countryMethods;
    }

    /**
     * Get url for dispatching customer to onboarding process
     * @param string $token
     * @return string
     */
    public function getOnboardingProcessUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd' => '_grant-permission',
            'request_token' => $token
        ));
    }

    /**
     * PayPal web URL generic getter
     *
     * @param array $params
     * @return string
     */
    public function getPaypalUrl(array $params = array())
    {
        return sprintf('https://www.%spaypal.com/cgi-bin/webscr%s',
            $this->sandboxFlag ? 'sandbox.' : '',
            $params ? '?' . http_build_query($params, '', '&') : ''
        );
    }

    /**
     * Check whether the specified payment method is a CC-based one
     *
     * @param string $code
     * @return bool
     */
    public static function getIsCreditCardMethod($code)
    {
        switch ($code) {
            case self::METHOD_DIRECT_BOARDING:
                return true;
        }

        return false;
    }

    /**
     * Get payment method status before save in config
     *
     * @deprecated
     * @param string $requestStatus
     * @return string
     */
    public function getMappedMethodStatus($requestStatus)
    {
        switch ($requestStatus) {
            case Saas_Paypal_Model_Api_Soap_Operation_GetBoardingDetails::BOARDING_STATUS_PENDING:
                $status = Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_PENDING;
                break;
            case Saas_Paypal_Model_Api_Soap_Operation_GetBoardingDetails::BOARDING_STATUS_COMPLETED:
                $status = Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE;
                break;
            case Saas_Paypal_Model_Api_Soap_Operation_GetBoardingDetails::BOARDING_STATUS_CANCELED:
                $status = Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_CANCELED;
                break;
            default:
                $status = Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_DISABLED;
                break;
        }

        return $status;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = null;
        switch ($this->_methodCode) {
            case self::METHOD_EXPRESS_BOARDING:
                $path = $this->_mapExpressFieldset($fieldName);
                break;
            case self::METHOD_DIRECT_BOARDING:
                $path = $this->_mapDirectFieldset($fieldName);
                break;
        }

        if ($path === null) {
            $path = $this->_mapBoardingFieldset($fieldName);
        }

        if ($path === null) {
            $path = $this->_mapGeneralFieldset($fieldName);
        }

        if ($path === null) {
            $path = $this->_mapGenericStyleFieldset($fieldName);
        }

        if ($path === null) {
            $path = parent::_getSpecificConfigPath($fieldName);
        }

        return $path;
    }

    /**
     * Map PayPal Website Payments Pro (onboarding) common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapBoardingFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'receiver_id':
            case 'boarding_account':
            case 'boarding_token':
            case 'boarding_token_lifetime':
            case 'program_code':
            case 'payment_method':
            case 'api_username':
            case 'api_password':
            case 'api_signature':
            case 'application_id':
            case 'sandbox_flag':
            case 'use_proxy':
            case 'proxy_host':
            case 'proxy_port':
            case 'button_flavor':
            case 'debug':
                return "paypal/onboarding/{$fieldName}";
            default:
                return null;
        }
    }

    /**
     * Returns business account value needed for IPN
     *
     * @return string
     */
    public function getIpnBusinessAccount()
    {
        return $this->boardingAccount;
    }

    /**
     * Returns if config was ever activated
     *
     * @param string $paymentMethod
     * @param mixed $store
     * @return bool
     */
    public function isWasActivated($paymentMethod, $store = null)
    {
        return Mage::getStoreConfigFlag("payment/{$paymentMethod}/was_activated", $store);
    }

    /**
     * Sets if config was ever activated
     *
     * @param string $paymentMethod
     * @param int $value
     * @param string $scope
     * @param int $scopeId
     * @return Saas_Paypal_Model_Boarding_Config
     */
    public function setWasActivated($paymentMethod, $value, $scope = 'default', $scopeId = 0)
    {
        $this->_configWriter->saveConfig('payment/'.$paymentMethod.'/was_activated',
            $value, $scope, $scopeId);
        return $this;
    }

    /**
     * Perform additional config value preparation and return new value if needed
     * overriden in Saas to support Permissions authorization type
     *
     * @param string $key Underscored key
     * @param string $value Old value
     * @return string Modified value or old value
     */
    protected function _prepareValue($key, $value)
    {
        $value = parent::_prepareValue($key, $value);
        // Always set payment action as "Sale" for Unilateral payments in EC
        if ($key == 'payment_action'
            && $value != self::PAYMENT_ACTION_SALE
            && $this->_methodCode == self::METHOD_EXPRESS_BOARDING
            && $this->shouldUseUnilateralPayments()
        ) {
            return self::PAYMENT_ACTION_SALE;
        }
        return $value;
    }

    /**
     * Check whether only Unilateral payments (Accelerated Boarding) possible for Express method or not
     *
     * @return bool
     */
    public function shouldUseUnilateralPayments()
    {
        return $this->boarding_account && $this->isWppApiAvailabe() &&
            $this->_getHelper('Saas_Paypal_Helper_Data')->isEcAcceleratedBoarding();
    }

    /**
     * Get store config value
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    protected function _getStoreConfig($path, $store = null)
    {
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Get store config flag value
     *
     * @param string $path
     * @param mixed $store
     * @return bool
     */
    protected function _getStoreConfigFlag($path, $store = null)
    {
        return Mage::getStoreConfigFlag($path, $store);
    }

    /**
     * Get helper
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }
}
