<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**#@+
     * Endpoint types with appropriate routes
     */
    const ENDPOINT_AUTHORIZE_CUSTOMER        = 'oauth/authorize';
    const ENDPOINT_AUTHORIZE_ADMIN           = 'adminhtml/oauth_authorize';
    const ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE = 'oauth/authorize/simple';
    const ENDPOINT_AUTHORIZE_ADMIN_SIMPLE    = 'adminhtml/oauth_authorize/simple';
    const ENDPOINT_INITIATE                  = 'oauth/initiate';
    const ENDPOINT_TOKEN                     = 'oauth/token';
    /**#@-*/

    /**#@+
     * Cleanup xpath config settings
     */
    const XML_PATH_CLEANUP_PROBABILITY       = 'oauth/cleanup/cleanup_probability';
    const XML_PATH_CLEANUP_EXPIRATION_PERIOD = 'oauth/cleanup/expiration_period';
    /**#@-*/

    /**#@+ Email template */
    const XML_PATH_EMAIL_TEMPLATE = 'oauth/email/template';
    const XML_PATH_EMAIL_IDENTITY = 'oauth/email/identity';
    /**#@-*/

    /**
     * Cleanup expiration period in minutes
     */
    const CLEANUP_EXPIRATION_PERIOD_DEFAULT = 120;

    /**
     * Query parameter as a sign that user rejects
     */
    const QUERY_PARAM_REJECTED = 'rejected';

    /**
     * Available endpoints list
     *
     * @var array
     */
    protected $_endpoints = array(
        self::ENDPOINT_AUTHORIZE_CUSTOMER,
        self::ENDPOINT_AUTHORIZE_ADMIN,
        self::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE,
        self::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE,
        self::ENDPOINT_INITIATE,
        self::ENDPOINT_TOKEN
    );

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /** @var Magento_Oauth_Model_Consumer_Factory */
    protected $_consumerFactory;

    /** @var Magento_Core_Model_Store */
    protected $_store;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Oauth_Model_Consumer_Factory $consumerFactory
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Oauth_Model_Consumer_Factory $consumerFactory,
        Magento_ObjectManager $objectManager
    ) {
        parent::__construct($context);
        $this->_coreData = $coreData;
        $this->_store = $storeManager->getStore();
        $this->_consumerFactory = $consumerFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * Generate random string for token or secret or verifier
     *
     * @param int $length String length
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _generateRandomString($length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is install. It provides a better randomness.
            $bytes = openssl_random_pseudo_bytes(ceil($length/2), $strong);
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $randomString = substr($hex, 0, $length); // truncate at most 1 char if length parameter is an odd number
        } else {
            // fallback to mt_rand() if openssl is not installed
            $randomString = $this->_coreData->getRandomString(
                $length, Magento_Core_Helper_Data::CHARS_DIGITS . Magento_Core_Helper_Data::CHARS_LOWERS
            );
        }

        return $randomString;
    }

    /**
     * Generate random string for token
     *
     * @return string
     */
    public function generateToken()
    {
        return $this->_generateRandomString(Magento_Oauth_Model_Token::LENGTH_TOKEN);
    }

    /**
     * Generate random string for token secret
     *
     * @return string
     */
    public function generateTokenSecret()
    {
        return $this->_generateRandomString(Magento_Oauth_Model_Token::LENGTH_SECRET);
    }

    /**
     * Generate random string for verifier
     *
     * @return string
     */
    public function generateVerifier()
    {
        return $this->_generateRandomString(Magento_Oauth_Model_Token::LENGTH_VERIFIER);
    }

    /**
     * Generate random string for consumer key
     *
     * @return string
     */
    public function generateConsumerKey()
    {
        return $this->_generateRandomString(Magento_Oauth_Model_Consumer::KEY_LENGTH);
    }

    /**
     * Generate random string for consumer secret
     *
     * @return string
     */
    public function generateConsumerSecret()
    {
        return $this->_generateRandomString(Magento_Oauth_Model_Consumer::SECRET_LENGTH);
    }

    /**
     * Return complete callback URL or boolean FALSE if no callback provided
     *
     * @param Magento_Oauth_Model_Token $token Token object
     * @param bool $rejected OPTIONAL Add user reject sign
     * @return bool|string
     */
    public function getFullCallbackUrl(Magento_Oauth_Model_Token $token, $rejected = false)
    {
        $callbackUrl = $token->getCallbackUrl();

        if (Magento_Oauth_Model_Server::CALLBACK_ESTABLISHED == $callbackUrl) {
            return false;
        }
        if ($rejected) {
            /** @var $consumer Magento_Oauth_Model_Consumer */
            $consumer = $this->_consumerFactory->create()->load($token->getConsumerId());

            if ($consumer->getId() && $consumer->getRejectedCallbackUrl()) {
                $callbackUrl = $consumer->getRejectedCallbackUrl();
            }
        } else if (!$token->getAuthorized()) {
            Mage::throwException('Token is not authorized');
        }

        $callbackUrl .= (false === strpos($callbackUrl, '?') ? '?' : '&');
        $callbackUrl .= 'oauth_token=' . $token->getToken() . '&';
        $callbackUrl .= $rejected ? self::QUERY_PARAM_REJECTED . '=1' : 'oauth_verifier=' . $token->getVerifier();

        return $callbackUrl;
    }

    /**
     * Retrieve URL of specified endpoint.
     *
     * @param string $type Endpoint type (one of ENDPOINT_ constants)
     * @return string
     * @throws Exception    Exception when endpoint not found
     */
    public function getProtocolEndpointUrl($type)
    {
        if (!in_array($type, $this->_endpoints)) {
            throw new Exception('Invalid endpoint type passed.');
        }
        return rtrim(Mage::getUrl($type), '/');
    }

    /**
     * Calculate cleanup possibility for data with lifetime property
     *
     * @return bool
     */
    public function isCleanupProbability()
    {
        // Safe get cleanup probability value from system configuration
        $configValue = (int) $this->_store->getConfig(self::XML_PATH_CLEANUP_PROBABILITY);
        return $configValue > 0 ? 1 == mt_rand(1, $configValue) : false;
    }

    /**
     * Get cleanup expiration period value from system configuration in minutes
     *
     * @return int
     */
    public function getCleanupExpirationPeriod()
    {
        $minutes = (int) $this->_store->getConfig(self::XML_PATH_CLEANUP_EXPIRATION_PERIOD);
        return $minutes > 0 ? $minutes : self::CLEANUP_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Send Email to Token owner
     *
     * @param string $userEmail
     * @param string $userName
     * @param string $applicationName
     * @param string $status
     */
    public function sendNotificationOnTokenStatusChange($userEmail, $userName, $applicationName, $status)
    {
        /* @var $mailTemplate Magento_Core_Model_Email_Template */
        $mailTemplate = $this->_objectManager->create('Magento_Core_Model_Email_Template');

        $mailTemplate->sendTransactional(
            $this->_store->getConfig(self::XML_PATH_EMAIL_TEMPLATE),
            $this->_store->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $userEmail,
            $userName,
            array(
                'name'              => $userName,
                'email'             => $userEmail,
                'applicationName'   => $applicationName,
                'status'            => $status,

            )
        );
    }

    /**
     * Is current authorize page is simple
     *
     * @return boolean
     */
    protected function _isSimple()
    {
        $simple = false;
        if (stristr($this->_getRequest()->getActionName(), 'simple')
            || !is_null($this->_getRequest()->getParam('simple', null))
        ) {
            $simple = true;
        }

        return $simple;
    }

    /**
     * Get authorize endpoint url
     *
     * @param string $userType
     * @throws Exception
     * @return string
     */
    public function getAuthorizeUrl($userType)
    {
        $simple = $this->_isSimple();

        if (Magento_Oauth_Model_Token::USER_TYPE_CUSTOMER == $userType) {
            if ($simple) {
                $route = self::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE;
            } else {
                $route = self::ENDPOINT_AUTHORIZE_CUSTOMER;
            }
        } elseif (Magento_Oauth_Model_Token::USER_TYPE_ADMIN == $userType) {
            if ($simple) {
                $route = self::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE;
            } else {
                $route = self::ENDPOINT_AUTHORIZE_ADMIN;
            }
        } else {
            throw new Exception('Invalid user type.');
        }

        return $this->_getUrl($route, array('_query' => array('oauth_token' => $this->getOauthToken())));
    }

    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    public function getOauthToken()
    {
        return $this->_getRequest()->getParam('oauth_token', null);
    }
}
