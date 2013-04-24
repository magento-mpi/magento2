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
 * Onboarding model
 *
 * @category   Saas
 * @package    Saas_Paypal
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Paypal_Model_Boarding_Onboarding
{
    /**
     * Onboarding payment method statuses
     *
     * @var string
     */
    const METHOD_STATUS_ACTIVE = 'active';
    const METHOD_STATUS_PENDING = 'pending';
    const METHOD_STATUS_DISABLED = 'disabled';
    const METHOD_STATUS_CANCELED = 'canceled';

    /**
     * Token lifetime in seconds
     *
     * @var int
     */
    const BOARDING_TOKEN_LIFETIME = 259200;

    /**
     * SOAP Api model
     *
     * @var Saas_Paypal_Model_Api_Soap
     */
    protected $_api = null;

    /**
     * Onboarding Config model
     *
     * @var Saas_Paypal_Model_Boarding_Config
     */
    protected $_config = null;

    /**
     * Api model string
     *
     * @var string
     */
    protected $_apiType = 'Saas_Paypal_Model_Api_Permission_Nvp';

    /**
     * Config model string
     *
     * @var string
     */
    protected $_configType = 'Saas_Paypal_Model_Boarding_Config';

    /**
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     */
    public function __construct(Mage_Core_Model_Config_Storage_WriterInterface $configWriter)
    {
        $this->_configWriter = $configWriter;
    }

    /**
     * Config instance getter
     *
     * @return Saas_Paypal_Model_Boarding_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = Mage::getModel($this->_configType);
        }
        return $this->_config;
    }

    /**
     * Get SOAP api model
     *
     * @return Saas_Paypal_Model_Api_Permission_Nvp
     */
    public function getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel($this->_apiType);
            $this->_api->setConfigObject($this->getConfig());
        }
        return $this->_api;
    }

    /**
     * Returns boarding URL to redirect the user to PayPal.
     *
     * @param string $token
     * @return string
     */
    public function getBoardingUrl($token)
    {
        return $this->getConfig()->getOnboardingProcessUrl($token);
    }

    /**
     * Sends EnterBoarding request to SOAP and gets the token.
     *
     * @param string $methodCode
     * @param int $storeId
     * @return string
     */
    public function enterBoarding($methodCode, $storeId = null)
    {
        $this->getConfig()->setStoreId($storeId);
        return $this->getApi()->requestPermissions($this->getConfig()->getPermissionGroups($methodCode));
    }

    /**
     * Returns boarding details by token.
     *
     * @param string $token
     * @param int $storeId
     * @return array
     */
    public function getBoardingDetails($token, $storeId = null)
    {
        $this->getConfig()->setStoreId($storeId);
        $api = $this->getApi();
        $api->setBoardingToken($token);
        $api->callGetBoardingDetails();
        return array(
            'boarding_status'   => $api->getBoardingStatus(),
            'boarding_account'  => $api->getBoardingAccount()
        );
    }

    /**
     * Do API cal and return payer id
     *
     * @param string $token
     * @param string $code
     * @return string
     */
    public function getPayerId($token, $code)
    {
        $api = $this->getApi();
        $details = $api->getAccessData($token, $code);

        if (!empty($details['token']) && !empty($details['tokenSecret'])) {
            $payerId = $api->getPayerId($details['token'], $details['tokenSecret']);

            $this->_saveConfig('paypal/onboarding/access_token', $details['token'])
                ->_saveConfig('paypal/onboarding/access_token_secret', $details['tokenSecret'])
                ->_saveConfig('paypal/onboarding/receiver_id', $payerId);

            return $payerId;
        }
        return '';
    }

    /**
     * Change method status
     *
     * @param string $method
     * @param string $status
     * @param bool $force
     */
    protected function _changeMethodStatus($method, $status, $force = false)
    {
        $configPath = sprintf('payment/%s/status', $method);

        if ($force || Mage::getStoreConfig($configPath) != $status) {
            $this->_saveConfig($configPath, $status);
        }
    }

    /**
     * Change method activity
     *
     * @param string $method
     * @param bool $activity
     * @param bool $force
     */
    protected function _changeMethodActivity($method, $activity, $force = false)
    {
        $configPath = sprintf('payment/%s/active', $method);

        if ($force || Mage::getStoreConfig($configPath) != $activity) {
            $this->_saveConfig($configPath, $activity);
            Mage::getSingleton('Mage_Backend_Model_Config')->setConfigDataValue($configPath, $activity);
        }
    }

    /**
     * Update method status
     *
     * @param string $token
     * @param string $code
     * @return Saas_Paypal_Model_Boarding_Onboarding
     */
    public function updateMethodStatus($token = '', $code = '')
    {
        $paymentMethod = $this->getConfig()->paymentMethod;
        try {
            $payerId = $this->getPayerId($token, $code);
        } catch (Mage_Core_Exception $e) {
            // PayPal session with the token is expired
            $payerId = false;
        }

        if ($paymentMethod && $payerId) {
            $this->_deactivateMethods();
            $this->getConfig()->setWasActivated($paymentMethod, 1);
            $this->_activateMethod($paymentMethod);
            $this->_resetStoreConfig();
            $this->_dispatchEventOnActivationSuccess();
        }

        return $this;
    }

    /**
     * Dispatch paypal_onboarding_activation_success event
     */
    protected function _dispatchEventOnActivationSuccess()
    {
        Mage::dispatchEvent('paypal_onboarding_activation_success', array('details' => array()));
    }

    /**
     * Reset store config
     */
    protected function _resetStoreConfig()
    {
        Mage::app()->getStore()->resetConfig();
    }

    /**
     * Get EnterBoarding token by SOAP EnterBoarding
     *
     * @param string $method
     * @return string
     */
    public function getEnterBoardingUrl($method)
    {
        $token = $this->enterBoarding($method);
        if (!$token) {
            Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('An error has occurred while boarding process.'));
        }

        $this->_saveConfig('paypal/onboarding/payment_method', $method);

        $this->_resetStoreConfig();

        return $this->getBoardingUrl($token);
    }

    /**
     * Activate current method
     *
     * @param string $method
     * @return Saas_Paypal_Model_Boarding_Onboarding
     */
    protected function _activateMethod($method)
    {
        $this->_changeMethodStatus($method, self::METHOD_STATUS_ACTIVE, true);
        $this->_changeMethodActivity($method, true, true);

        $authConfigPath = $method == Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING
            ? ('payment/'. Mage_Paypal_Model_Config::METHOD_WPP_DIRECT .'/authentication_method')
            : ('payment/'. Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING .'/authentification_method');

        $this->_saveConfig(
            $authConfigPath,
            Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS
        );

        if ($method == Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING) {
            $this->_activateMethod(Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING);
        } elseif ($method == Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING) {
            Mage::getResourceSingleton('Mage_Sales_Model_Resource_Order_Payment')->updatePaymentMethodName(
                Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS,
                Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING
            );
        }

        return $this;
    }

    /**
     * Deactivate previous state of methods (all in current country)
     *
     * @return Saas_Paypal_Model_Boarding_Onboarding
     */
    protected function _deactivateMethods()
    {
        $countryCode = $this->_getMerchantCountryCode();
        if (empty($countryCode)) {
            $countryCode = $this->_getDefaultCountryCode();
        }
        foreach ($this->getConfig()->getCountryMethods($countryCode) as $code) {
            // Do not disable BA when granting permissions
            if ($code == Mage_Paypal_Model_Config::METHOD_BILLING_AGREEMENT) {
                continue;
            }
            $this->_changeMethodActivity($code, false);
            $this->_changeMethodStatus($code, self::METHOD_STATUS_DISABLED);
        }

        return $this;
    }

    /**
     * Get current merchant country code
     *
     * @return string
     */
    protected function _getMerchantCountryCode()
    {
        return (string)Mage::getSingleton('Mage_Backend_Model_Config')
            ->getConfigDataValue('paypal/general/merchant_country');
    }

    /**
     * Get default country code
     *
     * @return string
     */
    protected function _getDefaultCountryCode()
    {
        return Mage::helper('Mage_Core_Helper_Data')->getDefaultCountry();
    }

    /**
     * @param $path
     * @param $value
     * @param string $scope
     * @param int $scopeId
     * @return $this
     */
    protected function _saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        $this->_configWriter->saveConfig($path, $value, $scope, $scopeId);
        return $this;
    }
}
