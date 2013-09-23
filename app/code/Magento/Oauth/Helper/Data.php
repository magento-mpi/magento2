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
    const ENDPOINT_AUTHORIZE_CUSTOMER = 'oauth/authorize';
    const ENDPOINT_AUTHORIZE_ADMIN = 'adminhtml/oauth_authorize';
    const ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE = 'oauth/authorize/simple';
    const ENDPOINT_AUTHORIZE_ADMIN_SIMPLE = 'adminhtml/oauth_authorize/simple';
    const ENDPOINT_INITIATE = 'oauth/initiate';
    const ENDPOINT_TOKEN = 'oauth/token';
    /**#@-*/

    /**#@+
     * Cleanup xpath config settings
     */
    const XML_PATH_CLEANUP_PROBABILITY = 'oauth/cleanup/cleanup_probability';
    const XML_PATH_CLEANUP_EXPIRATION_PERIOD = 'oauth/cleanup/expiration_period';
    /**#@-*/

    /**#@+
     * Consumer config settings
     */
    const XML_PATH_CONSUMER_EXPIRATION_PERIOD = 'oauth/consumer/expiration_period';

    /**
     * Cleanup expiration period in minutes
     */
    const CLEANUP_EXPIRATION_PERIOD_DEFAULT = 120;

    /**
     * Consumer expiration period in seconds
     */
    const CONSUMER_EXPIRATION_PERIOD_DEFAULT = 300;

    /**
     * Query parameter as a sign that user rejects
     */
    const QUERY_PARAM_REJECTED = 'rejected';

    /**
     * Value of callback URL when it is established or if the client is unable to receive callbacks
     *
     * @link http://tools.ietf.org/html/rfc5849#section-2.1     Requirement in RFC-5849
     */
    const CALLBACK_ESTABLISHED = 'oob';

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

    /**#@+
     * OAuth result statuses
     */
    const ERR_OK = 0;
    const ERR_VERSION_REJECTED = 1;
    const ERR_PARAMETER_ABSENT = 2;
    const ERR_PARAMETER_REJECTED = 3;
    const ERR_TIMESTAMP_REFUSED = 4;
    const ERR_NONCE_USED = 5;
    const ERR_SIGNATURE_METHOD_REJECTED = 6;
    const ERR_SIGNATURE_INVALID = 7;
    const ERR_CONSUMER_KEY_REJECTED = 8;
    const ERR_TOKEN_USED = 9;
    const ERR_TOKEN_EXPIRED = 10;
    const ERR_TOKEN_REVOKED = 11;
    const ERR_TOKEN_REJECTED = 12;
    const ERR_VERIFIER_INVALID = 13;
    const ERR_PERMISSION_UNKNOWN = 14;
    const ERR_PERMISSION_DENIED = 15;
    const ERR_METHOD_NOT_ALLOWED = 16;
    const ERR_CONSUMER_KEY_INVALID = 17;
    /**#@-*/

    /**#@+
     * Signature Methods
     */
    const SIGNATURE_SHA1 = 'HMAC-SHA1';
    const SIGNATURE_SHA256 = 'HMAC-SHA256';
    /**#@-*/

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@-*/

    /**
     * Error code to error messages pairs
     *
     * @var array
     */
    protected $_errors = array(
        self::ERR_VERSION_REJECTED => 'version_rejected',
        self::ERR_PARAMETER_ABSENT => 'parameter_absent',
        self::ERR_PARAMETER_REJECTED => 'parameter_rejected',
        self::ERR_TIMESTAMP_REFUSED => 'timestamp_refused',
        self::ERR_NONCE_USED => 'nonce_used',
        self::ERR_SIGNATURE_METHOD_REJECTED => 'signature_method_rejected',
        self::ERR_SIGNATURE_INVALID => 'signature_invalid',
        self::ERR_CONSUMER_KEY_REJECTED => 'consumer_key_rejected',
        self::ERR_CONSUMER_KEY_INVALID => 'consumer_key_invalid',
        self::ERR_TOKEN_USED => 'token_used',
        self::ERR_TOKEN_EXPIRED => 'token_expired',
        self::ERR_TOKEN_REVOKED => 'token_revoked',
        self::ERR_TOKEN_REJECTED => 'token_rejected',
        self::ERR_VERIFIER_INVALID => 'verifier_invalid',
        self::ERR_PERMISSION_UNKNOWN => 'permission_unknown',
        self::ERR_PERMISSION_DENIED => 'permission_denied',
        self::ERR_METHOD_NOT_ALLOWED => 'method_not_allowed'
    );

    /**
     * TODO: Possible combine both the error objects
     * Error code to HTTP error code
     *
     * @var array
     */
    protected $_errorsToHttpCode = array(
        self::ERR_VERSION_REJECTED => self::HTTP_BAD_REQUEST,
        self::ERR_PARAMETER_ABSENT => self::HTTP_BAD_REQUEST,
        self::ERR_PARAMETER_REJECTED => self::HTTP_BAD_REQUEST,
        self::ERR_TIMESTAMP_REFUSED => self::HTTP_BAD_REQUEST,
        self::ERR_NONCE_USED => self::HTTP_UNAUTHORIZED,
        self::ERR_SIGNATURE_METHOD_REJECTED => self::HTTP_BAD_REQUEST,
        self::ERR_SIGNATURE_INVALID => self::HTTP_UNAUTHORIZED,
        self::ERR_CONSUMER_KEY_REJECTED => self::HTTP_UNAUTHORIZED,
        self::ERR_CONSUMER_KEY_INVALID => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_USED => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_EXPIRED => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_REVOKED => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_REJECTED => self::HTTP_UNAUTHORIZED,
        self::ERR_VERIFIER_INVALID => self::HTTP_UNAUTHORIZED,
        self::ERR_PERMISSION_UNKNOWN => self::HTTP_UNAUTHORIZED,
        self::ERR_PERMISSION_DENIED => self::HTTP_UNAUTHORIZED
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
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2), $strong);
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $randomString = substr($hex, 0, $length); // truncate at most 1 char if length parameter is an odd number
        } else {
            // fallback to mt_rand() if openssl is not installed
            $randomString = $this->_coreData->getRandomString(
                $length,
                Magento_Core_Helper_Data::CHARS_DIGITS . Magento_Core_Helper_Data::CHARS_LOWERS
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
     * Calculate cleanup possibility for data with lifetime property
     *
     * @return bool
     */
    public function isCleanupProbability()
    {
        // Safe get cleanup probability value from system configuration
        $configValue = (int)$this->_store->getConfig(self::XML_PATH_CLEANUP_PROBABILITY);
        return $configValue > 0 ? 1 == mt_rand(1, $configValue) : false;
    }

    /**
     * Get cleanup expiration period value from system configuration in minutes
     *
     * @return int
     */
    public function getCleanupExpirationPeriod()
    {
        $minutes = (int)$this->_store->getConfig(self::XML_PATH_CLEANUP_EXPIRATION_PERIOD);
        return $minutes > 0 ? $minutes : self::CLEANUP_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Get consumer expiration period value from system configuration in seconds
     *
     * @return int
     */
    public function getConsumerExpirationPeriod()
    {
        $seconds = (int)$this->_store->getConfig(self::XML_PATH_CONSUMER_EXPIRATION_PERIOD);
        return $seconds > 0 ? $seconds : self::CONSUMER_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Process HTTP request object and prepare for token validation
     *
     * @param Zend_Controller_Request_Http $httpRequest
     * @return array
     */
    public function _prepareServiceRequest($httpRequest)
    {
        //TODO: Fix needed for $this->getRequest()->getHttpHost(). Hosts with port are not covered
        $requestUrl = $httpRequest->getScheme() . '://' . $httpRequest->getHttpHost() .
            $httpRequest->getRequestUri();

        $serviceRequest = array();
        $serviceRequest['request_url'] = $requestUrl;
        $serviceRequest['http_method'] = $httpRequest->getMethod();

        $oauthParams = $this->_processRequest($httpRequest->getHeader('Authorization'),
                                              $httpRequest->getHeader(Zend_Http_Client::CONTENT_TYPE),
                                              $httpRequest->getRawBody(),
                                              $requestUrl);

        return array_merge($serviceRequest, $oauthParams);
    }

    /**
     * Process oauth related protocol information and return as an array
     *
     * @param $authHeaderValue
     * @param $contentTypeHeader
     * @param $requestBodyString
     * @param $requestUrl
     * @return array
     */
    protected function _processRequest($authHeaderValue, $contentTypeHeader, $requestBodyString, $requestUrl)
    {
        $protocolParams = array();

        if ($authHeaderValue && 'oauth' === strtolower(substr($authHeaderValue, 0, 5))) {
            $authHeaderValue = substr($authHeaderValue, 6); // ignore 'OAuth ' at the beginning

            foreach (explode(',', $authHeaderValue) as $paramStr) {
                $nameAndValue = explode('=', trim($paramStr), 2);

                if (count($nameAndValue) < 2) {
                    continue;
                }
                if ($this->_isProtocolParameter($nameAndValue[0])) {
                    $protocolParams[rawurldecode($nameAndValue[0])] = rawurldecode(trim($nameAndValue[1], '"'));
                }
            }
        }

        if ($contentTypeHeader && 0 === strpos($contentTypeHeader, Zend_Http_Client::ENC_URLENCODED)) {
            $protocolParamsNotSet = !$protocolParams;

            parse_str($requestBodyString, $protocolBodyParams);

            foreach ($protocolBodyParams as $bodyParamName => $bodyParamValue) {
                if (!$this->_isProtocolParameter($bodyParamName)) {
                    $protocolParams[$bodyParamName] = $bodyParamValue;
                } elseif ($protocolParamsNotSet) {
                    $protocolParams[$bodyParamName] = $bodyParamValue;
                }
            }
        }
        $protocolParamsNotSet = !$protocolParams;

        if (($queryString = Zend_Uri_Http::fromString($requestUrl)->getQuery())) {
            foreach (explode('&', $queryString) as $paramToValue) {
                $paramData = explode('=', $paramToValue);

                if (2 === count($paramData) && !$this->_isProtocolParameter($paramData[0])) {
                    $protocolParams[rawurldecode($paramData[0])] = rawurldecode($paramData[1]);
                }
            }
        }
        if ($protocolParamsNotSet) {
            $this->_fetchProtocolParamsFromQuery($protocolParams, $queryString);
        }

        // Combine request and header parameters
        return $protocolParams;
    }

    /**
     * Create response string for problem during request and set HTTP error code
     *
     * @param Exception $exception
     * @param Zend_Controller_Response_Http $response OPTIONAL If NULL - will use internal getter
     * @return string
     */
    public function prepareErrorResponse(
        Exception $exception,
        Zend_Controller_Response_Http $response = null
    ) {
        $errorMap = $this->_errors;
        $errorsToHttpCode = $this->_errorsToHttpCode;

        $eMsg = $exception->getMessage();

        if ($exception instanceof Magento_Oauth_Exception) {
            $eCode = $exception->getCode();

            if (isset($errorMap[$eCode])) {
                $errorMsg = $errorMap[$eCode];
                $responseCode = $errorsToHttpCode[$eCode];
            } else {
                $errorMsg = 'unknown_problem&code=' . $eCode;
                $responseCode = self::HTTP_INTERNAL_ERROR;
            }
            if (self::ERR_PARAMETER_ABSENT == $eCode) {
                $errorMsg .= '&oauth_parameters_absent=' . $eMsg;
            } elseif ($eMsg) {
                $errorMsg .= '&message=' . $eMsg;
            }
        } else {
            $errorMsg = 'internal_error&message=' . ($eMsg ? $eMsg : 'empty_message');
            $responseCode = self::HTTP_INTERNAL_ERROR;
        }

        $response->setHttpResponseCode($responseCode);
        return array('oauth_problem' => $errorMsg);
    }

    /**
     * Retrieve protocol parameters from query string
     *
     * @param $protocolParams
     * @param $queryString
     */
    protected function _fetchProtocolParamsFromQuery(&$protocolParams, $queryString)
    {
        foreach ($queryString as $queryParamName => $queryParamValue) {
            if ($this->_isProtocolParameter($queryParamName)) {
                $protocolParams[$queryParamName] = $queryParamValue;
            }
        }
    }

    /**
     * Check if attribute is oAuth related
     *
     * @param string $attrName
     * @return bool
     */
    protected function _isProtocolParameter($attrName)
    {
        return (bool)preg_match('/oauth_[a-z_-]+/', $attrName);
    }
}
