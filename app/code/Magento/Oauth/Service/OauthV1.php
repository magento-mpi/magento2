<?php
/**
 * Web API Oauth Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * TODO: Reduce PHPMD Coupling Between Objects
 * Class Magento_Oauth_Service_OauthV1
 */
class Magento_Oauth_Service_OauthV1 implements Magento_Oauth_Service_OauthV1Interface
{
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
        self::ERR_TOKEN_USED => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_EXPIRED => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_REVOKED => self::HTTP_UNAUTHORIZED,
        self::ERR_TOKEN_REJECTED => self::HTTP_UNAUTHORIZED,
        self::ERR_VERIFIER_INVALID => self::HTTP_UNAUTHORIZED,
        self::ERR_PERMISSION_UNKNOWN => self::HTTP_UNAUTHORIZED,
        self::ERR_PERMISSION_DENIED => self::HTTP_UNAUTHORIZED
    );

    /**
     * Possible time deviation for timestamp validation in sec.
     */
    const TIME_DEVIATION = 600;

    /**#@+
     * Request Types
     */
    const REQUEST_AUTHORIZE = 'authorize'; // display authorize form
    const REQUEST_TOKEN = 'token'; // ask for permanent credentials
    const REQUEST_RESOURCE = 'resource'; // ask for protected resource using permanent credentials

    /** @var  Magento_Oauth_Model_Consumer_Factory */
    private $_consumerFactory;

    /** @var  Magento_Oauth_Model_Nonce_Factory */
    private $_nonceFactory;

    /** @var  Magento_Oauth_Model_Token_Factory */
    private $_tokenFactory;

    /** @var  Magento_Oauth_Helper_Data */
    protected $_helper;

    /** @var  Magento_Core_Model_StoreManagerInterface */
    protected $_storeManager;

    /** @var  Magento_HTTP_ZendClient */
    protected $_httpClient;

    /**#@+
     * Required parameters for each token operation
     */
    protected $_requiredGeneric = array(
        "oauth_consumer_key",
        "oauth_signature",
        "oauth_signature_method",
        "oauth_nonce",
        "oauth_timestamp"
    );

    protected $_requiredAccess = array(
        "oauth_consumer_key",
        "oauth_signature",
        "oauth_signature_method",
        "oauth_nonce",
        "oauth_timestamp",
        "oauth_token",
        "oauth_verifier"
    );

    protected $_requiredValidate = array(
        "oauth_consumer_key",
        "oauth_signature",
        "oauth_signature_method",
        "oauth_nonce",
        "oauth_timestamp",
        "oauth_token"
    );

    /**
     * @param Magento_Oauth_Model_Consumer_Factory $consumerFactory
     * @param Magento_Oauth_Model_Nonce_Factory $nonceFactory
     * @param Magento_Oauth_Model_Token_Factory $tokenFactory
     * @param Magento_Oauth_Helper_Data $helper
     * @param Magento_Core_Model_StoreManagerInterface
     * @param Magento_HTTP_ZendClient
     */
    public function __construct(
        Magento_Oauth_Model_Consumer_Factory $consumerFactory,
        Magento_Oauth_Model_Nonce_Factory $nonceFactory,
        Magento_Oauth_Model_Token_Factory $tokenFactory,
        Magento_Oauth_Helper_Data $helper,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_HTTP_ZendClient $httpClient
    ) {
        $this->_consumerFactory = $consumerFactory;
        $this->_nonceFactory = $nonceFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_helper = $helper;
        $this->_store = $storeManager->getStore();
        $this->_httpClient = $httpClient;
    }

    /**
     * Retrieve array of supported signature methods.
     *
     * @return array - Supported HMAC-SHA1 and HMAC-SHA256 signature methods.
     */
    public static function getSupportedSignatureMethods()
    {
        return array(self::SIGNATURE_SHA1, self::SIGNATURE_SHA256);
    }

    /**
     * Create a new consumer account when an Add-On is installed.
     *
     * @param array $consumerData - Information provided by the Add-On when the Add-On is installed.
     * @return array - The Add-On (consumer) data.
     * @throws Magento_Core_Exception
     * @throws Magento_Oauth_Exception
     */
    public function createConsumer($consumerData)
    {
        try {
            $consumer = $this->_consumerFactory->create($consumerData);
            $consumer->save();
            return $consumer->getData();
        } catch (Magento_Core_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new Magento_Oauth_Exception(__('Unexpected error. Unable to create OAuth Consumer account.'));
        }
    }

    /**
     * Perform post to Add-On (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param array $consumerData - The Add-On (consumer) data.
     * @return array - The oauth_verifier.
     * @throws Magento_Core_Exception
     * @throws Magento_Oauth_Exception
     */
    public function postToConsumer($consumerData)
    {
        try {
            $storeUrl = $this->_store->getBaseUrl();
            $this->_httpClient->setUri($consumerData['http_post_url']);
            $this->_httpClient->setParameterPost(array(
                'oauth_consumer_key' => $consumerData['key'],
                'oauth_consumer_secret' => $consumerData['secret'],
                'store_url' => $storeUrl
            ));
            // TODO: Uncomment this when there is a live http_post_url that we can actually post to.
            //$this->_httpClient->request(Magento_HTTP_ZendClient::POST);

            $verifier = $this->_tokenFactory->create()->createVerifierToken($consumerData['entity_id']);
            return array('oauth_verifier' => $verifier->getVerifier());
        } catch (Magento_Core_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new Magento_Oauth_Exception(__('Unexpected error. Unable to post data to consumer.'));
        }
    }

    /**
     * Issue a pre-authorization request token to the caller.
     *
     * @param array $signedRequest - Parameters (e.g. consumer key, nonce, signature method, etc.)
     * @return array - The oauth_token and oauth_token_secret pair.
     * @throws Magento_Oauth_Exception
     */
    public function getRequestToken($signedRequest)
    {
        $signedRequest = $this->_processTokenRequest($signedRequest);

        $this->_validateVersionParam($signedRequest['oauth_version']);

        $consumer = $this->_getConsumerByKey($signedRequest['oauth_consumer_key']);
        $this->_validateNonce($signedRequest['nonce'], $consumer->getId(), $signedRequest['oauth_timestamp']);

        $token = $this->_getTokenByConsumer($consumer->getId());

        if ($token->getType() != Magento_Oauth_Model_Token::TYPE_VERIFIER) {
            throw new Magento_Oauth_Exception('', self::ERR_TOKEN_REJECTED);
        }

        //OAuth clients are not sending the verifier param for requestToken requests
        //$this->_validateVerifierParam($signedRequest['oauth_verifier'], $token->getVerifier());

        $this->_validateSignature(
            $signedRequest,
            $consumer->getSecret(),
            $signedRequest['http_method'],
            $signedRequest['request_url']
        );

        //TODO:Check if we need callback URL here. Currently it errors out if not supplied
        $requestToken = $token->createRequestToken($token->getId(), $consumer->getCallBackUrl());
        return array('oauth_token' => $requestToken->getToken(), 'oauth_token_secret' => $requestToken->getSecret());
    }

    /**
     * TODO: log the request token in dev mode since its not persisted
     * Get an access token in exchange for a pre-authorized token.
     * Perform appropriate parameter and signature validation.
     *
     * @param array $request - Parameters (e.g. consumer key, nonce, signature method, etc.)
     * @return array - The oauth_token and oauth_token_secret pair.
     * @throws Magento_Oauth_Exception
     */
    public function getAccessToken($request)
    {
        $request = $this->_processTokenRequest($request);

        // Make generic validation of request parameters
        $this->_validateProtocolParams($request, $this->_requiredAccess);

        $oauthToken = $request['oauth_token'];
        $requestUrl = $request['request_url'];
        $httpMethod = $request['http_method'];
        $consumerKeyParam = $request['oauth_consumer_key'];

        $consumer = $this->_getConsumerByKey($consumerKeyParam);
        $token = $this->_getToken($oauthToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        // The pre-auth token has a value of "request" in the type when it is requested and created initially.
        // In this flow (token flow) the token has to be of type "request" else its marked as reused.
        // TODO: Need to check security implication of this message
        if (Magento_Oauth_Model_Token::TYPE_REQUEST != $token->getType()) {
            $this->_throwException('', self::ERR_TOKEN_USED);
        }

        $this->_validateVerifierParam($request['oauth_verifier'], $token->getVerifier());

        // Need to unset and remove unnecessary params from the requestTokenData array.
        unset($request['request_url']);
        unset($request['http_method']);

        $this->_validateSignature(
            $request,
            $consumer->getSecret(),
            $httpMethod,
            $requestUrl,
            $token->getSecret()
        );

        $accessToken = $token->convertToAccess();
        return array('oauth_token' => $accessToken->getToken(), 'oauth_token_secret' => $accessToken->getSecret());
    }

    /**
     * Validate a requested access token
     *
     * @param array $request - Parameters (e.g. consumer key, nonce, signature method, etc.)
     * @return boolean - True if the access token is valid.
     * @throws Magento_Oauth_Exception
     */
    public function validateAccessToken($request)
    {
        $request = $this->_processTokenRequest($request);

        $oauthToken = $request['oauth_token'];
        $requestUrl = $request['request_url'];
        $httpMethod = $request['http_method'];
        $consumerKey = $request['oauth_consumer_key'];

        // make generic validation of request parameters
        $this->_validateProtocolParams($request, $this->_requiredValidate);

        $consumer = $this->_getConsumerByKey($consumerKey);
        $token = $this->_getToken($oauthToken);

        //TODO: Verify if we need to check the association in token validation
        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        if (Magento_Oauth_Model_Token::TYPE_ACCESS != $token->getType()) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }
        if ($token->getRevoked()) {
            $this->_throwException('', self::ERR_TOKEN_REVOKED);
        }

        // Need to unset and remove unnecessary params from the requestTokenData array.
        unset($request['request_url']);
        unset($request['http_method']);

        $this->_validateSignature(
            $request,
            $consumer->getSecret(),
            $httpMethod,
            $requestUrl,
            $token->getSecret()
        );

        // If no exceptions were raised return as a valid token
        return true;
    }

    /**
     * Validate (oauth_nonce) Nonce string.
     *
     * @param string $nonce - Nonce string
     * @param int $consumerId - Consumer Id (Entity Id)
     * @param string|int $timestamp - Unix timestamp
     * @throws Magento_Oauth_Exception
     */
    protected function _validateNonce($nonce, $consumerId, $timestamp)
    {
        try {
            $timestamp = (int)$timestamp;
            if ($timestamp <= 0 || $timestamp > (time() + self::TIME_DEVIATION)) {
                throw new Magento_Oauth_Exception(
                    __('Incorrect timestamp value in the oauth_timestamp parameter.'),
                    self::ERR_TIMESTAMP_REFUSED
                );
            }

            $nonceObj = $this->_getNonce($nonce, $consumerId);

            if ($nonceObj->getConsumerId()) {
                throw new Magento_Oauth_Exception(
                    __('The nonce is already being used by the consumer with id %1.', $consumerId),
                    self::ERR_NONCE_USED
                );
            }

            $consumer = $this->_getConsumer($consumerId);

            if ($nonceObj->getTimestamp() == $timestamp) {
                throw new Magento_Oauth_Exception(
                    __('The nonce/timestamp combination has already been used.'), self::ERR_NONCE_USED);
            }

            $nonceObj->setNonce($nonce)
                ->setConsumerId($consumer->getId())
                ->setTimestamp($timestamp)
                ->save();
        } catch (Magento_Oauth_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new Magento_Oauth_Exception(__('An error occurred validating the nonce.'));
        }
    }

    /**
     * Validate 'oauth_verifier' parameter
     *
     * @param string $verifier
     * @param string $verifierFromToken
     * @throws Magento_Oauth_Exception
     */
    protected function _validateVerifierParam($verifier, $verifierFromToken)
    {
        if (!is_string($verifier)) {
            throw new Magento_Oauth_Exception('', self::ERR_VERIFIER_INVALID);
        }
        if (strlen($verifier) != Magento_Oauth_Model_Token::LENGTH_VERIFIER) {
            throw new Magento_Oauth_Exception('', self::ERR_VERIFIER_INVALID);
        }
        if ($verifierFromToken != $verifier) {
            throw new Magento_Oauth_Exception('', self::ERR_VERIFIER_INVALID);
        }
    }

    /**
     * Validate signature based on the signature method used
     *
     * @param array $params
     * @param string $consumerSecret
     * @param string $httpMethod
     * @param string $requestUrl
     * @param string $tokenSecret
     * @throws Magento_Oauth_Exception
     */
    protected function _validateSignature($params, $consumerSecret, $httpMethod, $requestUrl, $tokenSecret = null)
    {
        if (!in_array($params['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            throw new Magento_Oauth_Exception('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        //Only allowable signature parameters
        $allowedSignParams = array(
            "oauth_callback",
            "oauth_consumer_key",
            "oauth_nonce",
            "oauth_signature_method",
            "oauth_timestamp",
            "oauth_version",
            "oauth_token",
            "oauth_verifier"
        );

        $util = new Zend_Oauth_Http_Utility();
        $calculatedSign = $util->sign(
            array_intersect_key($params, array_flip($allowedSignParams)),
            $params['oauth_signature_method'],
            $consumerSecret,
            $tokenSecret,
            $httpMethod,
            $requestUrl
        );

        if ($calculatedSign != $params['oauth_signature']) {
            $this->_throwException('Invalid signature.', self::ERR_SIGNATURE_INVALID);
        }
    }

    /**
     * Validate oauth version
     *
     * @param string $version
     * @throws Magento_Oauth_Exception
     */
    protected function _validateVersionParam($version)
    {
        // validate version if specified
        if ('1.0' != $version) {
            throw new Magento_Oauth_Exception('', self::ERR_VERSION_REJECTED);
        }
    }

    /**
     * Validate request and header parameters
     *
     * @param $protocolParams
     * @param $requiredParams
     */
    protected function _validateProtocolParams($protocolParams, $requiredParams)
    {
        // validate version if specified
        if (isset($protocolParams['oauth_version']) && '1.0' != $protocolParams['oauth_version']) {
            $this->_throwException('', self::ERR_VERSION_REJECTED);
        }
        // required parameters validation. Default to generic params if no provided
        if (empty($requiredParams)) {
            $requiredParams = $this->_requiredGeneric;
        }
        $this->_checkRequiredParams($protocolParams, $requiredParams);

        if (isset($protocolParams['oauth_token']) && strlen(
                $protocolParams['oauth_token']
            ) != Magento_Oauth_Model_Token::LENGTH_TOKEN
        ) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        // validate parameters type
        //TODO Need to verify if this is required
        foreach ($protocolParams as $paramName => $paramValue) {
            if (!is_string($paramValue)) {
                $this->_throwException($paramName, self::ERR_PARAMETER_REJECTED);
            }
        }
        // validate signature method
        if (!in_array($protocolParams['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            $this->_throwException('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        $consumer = $this->_getConsumerByKey($protocolParams['oauth_consumer_key']);

        $this->_validateNonce($protocolParams['oauth_nonce'], $consumer->getId(), $protocolParams['oauth_timestamp']);
    }

    /**
     * Get consumer by consumer_id
     *
     * @param $consumerId
     * @return Magento_Oauth_Model_Consumer
     */
    protected function _getConsumer($consumerId)
    {
        $consumer = $this->_consumerFactory->create()->load($consumerId);

        if (!$consumer->getId()) {
            $this->_throwException('', self::ERR_PARAMETER_REJECTED);
        }

        return $consumer;
    }

    /**
     * Get a consumer from its key
     *
     * @param string $consumerKey to load
     * @return Magento_Oauth_Model_Consumer
     * @throws Magento_Oauth_Exception
     */
    protected function _getConsumerByKey($consumerKey)
    {
        if (strlen($consumerKey) != Magento_Oauth_Model_Consumer::KEY_LENGTH) {
            throw new Magento_Oauth_Exception('', self::ERR_CONSUMER_KEY_REJECTED);
        }

        $consumer = $this->_consumerFactory->create()->loadByKey($consumerKey);

        if (!$consumer->getId()) {
            throw new Magento_Oauth_Exception('', self::ERR_CONSUMER_KEY_REJECTED);
        }

        return $consumer;
    }

    /**
     * Load token object, validate it depending on request type, set access data and save
     *
     * @param string $token
     * @return Magento_Oauth_Model_Token
     * @throws Magento_Oauth_Exception
     */
    protected function _getToken($token)
    {
        if (strlen($token) != Magento_Oauth_Model_Token::LENGTH_TOKEN) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        $tokenObj = $this->_tokenFactory->create()->load($token, 'token');

        if (!$tokenObj->getId()) {
            throw new Magento_Oauth_Exception('', self::ERR_TOKEN_REJECTED);
        }

        return $tokenObj;
    }

    /**
     * Load token object given a consumer id
     *
     * @param int $consumerId - The id of the consumer
     * @return Magento_Oauth_Model_Token
     * @throws Magento_Oauth_Exception
     */
    protected function _getTokenByConsumer($consumerId)
    {
        $token = $this->_tokenFactory->create()->load($consumerId, 'consumer_id');

        if (!$token->getId()) {
            throw new Magento_Oauth_Exception('', self::ERR_TOKEN_REJECTED);
        }

        return $token;
    }

    /**
     * Fetch nonce based on a composite key consisting of the nonce string and a consumer id.
     *
     * @param string $nonce - The nonce string
     * @param int $consumerId - A consumer id
     * @return Magento_Oauth_Model_Nonce
     */
    protected function _getNonce($nonce, $consumerId)
    {
        $nonceObj = $this->_nonceFactory->create()->loadByCompositeKey($nonce, $consumerId);
        return $nonceObj;
    }

    /**
     * Check if token belongs to the same consumer
     *
     * @param $token Magento_Oauth_Model_Token
     * @param $consumer Magento_Oauth_Model_Consumer
     * @return boolean
     */
    protected function _isTokenAssociatedToConsumer($token, $consumer)
    {
        return $token->getConsumerId() == $consumer->getId();
    }

    /**
     * Throw OAuth exception
     *
     * @param string $message Exception message
     * @param int $code Exception code
     * @throws Magento_Oauth_Exception
     */
    protected function _throwException($message = '', $code = 0)
    {
        throw new Magento_Oauth_Exception($message, $code);
    }

    /**
     * Get map of error code and error message
     *
     * @return array
     */
    public function getErrorMap()
    {
        return $this->_errors;
    }

    /**
     * Get map of error code and HTTP code
     *
     * @return array
     */
    public function getErrorToHttpCodeMap()
    {
        return $this->_errorsToHttpCode;
    }

    /**
     * Check if mandatory OAuth parameters are present
     *
     * @param $protocolParams
     * @param $requiredParams
     * @return mixed
     */
    protected function _checkRequiredParams($protocolParams, $requiredParams)
    {
        foreach ($requiredParams as $param) {
            if (!isset($protocolParams[$param])) {
                $this->_throwException($param, self::ERR_PARAMETER_ABSENT);
            }
        }
    }

    /**
     * Process token requests to extract Request parameters
     *
     * @param $request
     * @return array
     */
    public function _processTokenRequest($request)
    {
        $requestParamArray = $request['request_parameters'];
        $params = $this->_processRequest($requestParamArray['oauth_header'], $requestParamArray['content_type'],
            $requestParamArray['request_body'], $request['request_url']);
        unset($request['request_parameters']);
        return array_merge($request, $params);
    }

    /**
     * TODO: Reduce PHPMD Cyclomatic Complexity and NPath Complexity
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
