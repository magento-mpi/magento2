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
 * TODO: Need to refactor to reduce coupling
 * Class Mage_Oauth_Service_OauthV1
 */
class Mage_Oauth_Service_OauthV1 implements Mage_Oauth_Service_OauthInterfaceV1
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

    /** @var  Mage_Oauth_Model_Consumer_Factory */
    private $_consumerFactory;

    /** @var  Mage_Oauth_Model_Nonce_Factory */
    private $_nonceFactory;

    /** @var  Mage_Oauth_Model_Token_Factory */
    private $_tokenFactory;

    /** @var  Mage_Core_Model_Translate */
    private $_translator;

    /** @var  Mage_Oauth_Helper_Data */
    protected $_helper;

    /** @var  Mage_Core_Model_Store */
    protected $_store;

    /** @var  Zend_Http_Client */
    protected $_httpClient;

    /**
     * @param Mage_Oauth_Model_Consumer_Factory $consumerFactory
     * @param Mage_Oauth_Model_Nonce_Factory $nonceFactory
     * @param Mage_Oauth_Model_Token_Factory $tokenFactory
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Store
     * @param Mage_Core_Model_Translate $translator
     * @param Zend_Http_Client
     */
    public function __construct(
        Mage_Oauth_Model_Consumer_Factory $consumerFactory,
        Mage_Oauth_Model_Nonce_Factory $nonceFactory,
        Mage_Oauth_Model_Token_Factory $tokenFactory,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Store $store,
        Mage_Core_Model_Translate $translator,
        Zend_Http_Client $httpClient
    ) {
        $this->_consumerFactory = $consumerFactory;
        $this->_nonceFactory = $nonceFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_helper = $helperFactory->get('Mage_Oauth_Helper_Data');
        $this->_store = $store;
        $this->_translator = $translator;
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
     * @throws Mage_Core_Exception
     * @throws Mage_Oauth_Exception
     */
    public function createConsumer($consumerData)
    {
        try {
            $consumer = $this->_consumerFactory->create($consumerData);
            $consumer->save();
            return $consumer->getData();
        } catch (Mage_Core_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new Mage_Oauth_Exception(
                $this->_translator->translate(array('Unexpected error. Unable to create OAuth Consumer account.')));
        }
    }

    /**
     * Perform post to Add-On (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param array $consumerData - The Add-On (consumer) data.
     * @return array - The oauth_verifier.
     * @throws Mage_Core_Exception
     * @throws Mage_Oauth_Exception
     */
    public function postToConsumer($consumerData)
    {
        try {
            // TODO: The getBaseUrl() method doesn't seem to return the correct store url.
            $storeUrl = $this->_store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $storeApiBaseUrl = $storeUrl . 'api';

            $this->_httpClient->setUri($consumerData['http_post_url']);
            $this->_httpClient->setParameterPost(array(
                    'oauth_consumer_key' => $consumerData['key'],
                    'oauth_consumer_secret' => $consumerData['secret'],
                    'store_url' => $storeUrl,
                    'store_api_base_url' => $storeApiBaseUrl
                ));
            // TODO: Uncomment this when there is a live http_post_url that we can actually post to.
            //$this->_httpClient->request(Zend_Http_Client::POST);

            $token = $this->_tokenFactory->create()->createVerifierToken($consumerData['entity_id']);
            return array('oauth_verifier' => $token->getVerifier());
        } catch (Mage_Core_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new Mage_Oauth_Exception(
                $this->_translator->translate(array('Unexpected error. Unable to post data to consumer.')));
        }
    }

    /**
     * Issue a pre-authorization request token to the caller.
     *
     * @param array $signedRequest - Parameters (e.g. consumer key, nonce, signature method, etc.)
     * @return array - The oauth_token and oauth_token_secret pair.
     * @throws Mage_Oauth_Exception
     */
    public function getRequestToken($signedRequest)
    {
        $this->_validateVersionParam($signedRequest['oauth_version']);

        $consumer = $this->_getConsumerByKey($signedRequest['oauth_consumer_key']);
        $this->_validateNonce($signedRequest['nonce'], $consumer->getId(), $signedRequest['oauth_timestamp']);

        $token = $this->_getTokenByConsumer($consumer->getId());

        if ($token->getType() != Mage_Oauth_Model_Token::TYPE_VERIFIER) {
            throw new Mage_Oauth_Exception('', self::ERR_TOKEN_REJECTED);
        }

        $this->_validateVerifierParam($signedRequest['oauth_verifier'], $token->getVerifier());

        $this->_validateSignature(
            $signedRequest,
            $consumer->getSecret(),
            $signedRequest['http_method'],
            $signedRequest['request_url']
        );

        $requestToken = $token->createRequestToken($consumer->getId(), null);
        return array('oauth_token' => $requestToken->getToken(), 'oauth_token_secret' => $requestToken->getSecret());
    }

    /**
     * TODO: log the request token in dev mode since its not persisted
     * Get an access token in exchange for a pre-authorized token.
     * Perform appropriate parameter and signature validation.
     *
     * @param array $request - Parameters (e.g. consumer key, nonce, signature method, etc.)
     * @return array - The oauth_token and oauth_token_secret pair.
     * @throws Mage_Oauth_Exception
     */
    public function getAccessToken($request)
    {
        // Make generic validation of request parameters
        $this->_validateVersionParam($request['oauth_version']);

        // Validate signature method
        if (!in_array($request['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            $this->_throwException('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        $consumer = $this->_getConsumerByKey($request['oauth_consumer_key']);
        $this->_validateNonce($request['oauth_nonce'], $consumer->getId(), $request['oauth_timestamp']);

        $oauthToken = $request['oauth_token'];
        $requestUrl = $request['request_url'];
        $httpMethod = $request['http_method'];

        $token = $this->_getToken($oauthToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        // The pre-auth token has a value of "request" in the type when it is requested and created initially.
        // In this flow (token flow) the token has to be of type "request" else its marked as reused.
        // TODO: Need to check security implication of this message
        if (Mage_Oauth_Model_Token::TYPE_REQUEST != $token->getType()) {
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
     * @throws Mage_Oauth_Exception
     */
    public function validateAccessToken($request)
    {
        $oauthToken = $request['oauth_token'];
        $requestUrl = $request['request_url'];
        $httpMethod = $request['http_method'];
        $consumerKey = $request['oauth_consumer_key'];

        // make generic validation of request parameters
        $this->_validateVersionParam($request['oauth_version']);

        // validate signature method
        if (!in_array($request['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            $this->_throwException('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        $consumer = $this->_getConsumerByKey($consumerKey);
        $this->_validateNonce($request['oauth_nonce'], $consumer->getId(), $request['oauth_timestamp']);

        $token = $this->_getToken($oauthToken);

        // TODO: Verify if we need to check the association in token validation
        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        if (Mage_Oauth_Model_Token::TYPE_ACCESS != $token->getType()) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }
        if ($token->getRevoked()) {
            $this->_throwException('', self::ERR_TOKEN_REVOKED);
        }

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
     * @throws Mage_Oauth_Exception
     */
    protected function _validateNonce($nonce, $consumerId, $timestamp)
    {
        try {
            $timestamp = (int)$timestamp;
            if ($timestamp <= 0 || $timestamp > (time() + self::TIME_DEVIATION)) {
                throw new Mage_Oauth_Exception(
                    $this->_translator->translate(
                        array('Incorrect timestamp value in the oauth_timestamp parameter.')
                    ),
                    self::ERR_TIMESTAMP_REFUSED);
            }

            $nonceObj = $this->_getNonce($nonce, $consumerId);

            if ($nonceObj->getConsumerId()) {
                throw new Mage_Oauth_Exception(
                    $this->_translator->translate(
                        array('The nonce is already being used by the consumer with id %s.', $consumerId)
                    ),
                    self::ERR_NONCE_USED);
            }

            $consumer = $this->_getConsumer($consumerId);

            if ($nonceObj->getTimestamp() == $timestamp) {
                throw new Mage_Oauth_Exception(
                    $this->_translator->translate(
                        array('The nonce/timestamp combination has already been used.')
                    ), self::ERR_NONCE_USED);
            }

            $nonceObj->setNonce($nonce)
                ->setConsumerId($consumer->getId())
                ->setTimestamp($timestamp)
                ->save();
        } catch (Mage_Oauth_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new Mage_Oauth_Exception(
                $this->_translator->translate(array('An error occurred validating the nonce.'))
            );
        }
    }

    /**
     * Validate 'oauth_verifier' parameter
     *
     * @param string $verifier
     * @param string $verifierFromToken
     * @throws Mage_Oauth_Exception
     */
    protected function _validateVerifierParam($verifier, $verifierFromToken)
    {
        if (!is_string($verifier)) {
            throw new Mage_Oauth_Exception('', self::ERR_VERIFIER_INVALID);
        }
        if (strlen($verifier) != Mage_Oauth_Model_Token::LENGTH_VERIFIER) {
            throw new Mage_Oauth_Exception('', self::ERR_VERIFIER_INVALID);
        }
        if ($verifierFromToken != $verifier) {
            throw new Mage_Oauth_Exception('', self::ERR_VERIFIER_INVALID);
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
     * @throws Mage_Oauth_Exception
     */
    protected function _validateSignature($params, $consumerSecret, $httpMethod, $requestUrl, $tokenSecret = null)
    {
        if (!in_array($params['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            throw new Mage_Oauth_Exception('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        $util = new Zend_Oauth_Http_Utility();
        $calculatedSign = $util->sign(
            $params,
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
     * @throws Mage_Oauth_Exception
     */
    protected function _validateVersionParam($version)
    {
        // validate version if specified
        if ('1.0' != $version) {
            throw new Mage_Oauth_Exception('', self::ERR_VERSION_REJECTED);
        }
    }

    /**
     * Get consumer by consumer_id
     *
     * @param $consumerId
     * @return Mage_Oauth_Model_Consumer
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
     * @return Mage_Oauth_Model_Consumer
     * @throws Mage_Oauth_Exception
     */
    protected function _getConsumerByKey($consumerKey)
    {
        if (strlen($consumerKey) != Mage_Oauth_Model_Consumer::KEY_LENGTH) {
            throw new Mage_Oauth_Exception('', self::ERR_CONSUMER_KEY_REJECTED);
        }

        $consumer = $this->_consumerFactory->create()->loadByKey($consumerKey);

        if (!$consumer->getId()) {
            throw new Mage_Oauth_Exception('', self::ERR_CONSUMER_KEY_REJECTED);
        }

        return $consumer;
    }

    /**
     * Load token object, validate it depending on request type, set access data and save
     *
     * @param string $token
     * @return Mage_Oauth_Model_Token
     * @throws Mage_Oauth_Exception
     */
    protected function _getToken($token)
    {
        if (strlen($token) != Mage_Oauth_Model_Token::LENGTH_TOKEN) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }

        $tokenObj =  $this->_tokenFactory->create()->load($token, 'token');

        if (!$tokenObj->getId()) {
            throw new Mage_Oauth_Exception('', self::ERR_TOKEN_REJECTED);
        }

        return $tokenObj;
    }

    /**
     * Load token object given a consumer id
     *
     * @param int $consumerId - The id of the consumer
     * @return Mage_Oauth_Model_Token
     * @throws Mage_Oauth_Exception
     */
    protected function _getTokenByConsumer($consumerId)
    {
        $token = $this->_tokenFactory->create()->load($consumerId, 'consumer_id');

        if (!$token->getId()) {
            throw new Mage_Oauth_Exception('', self::ERR_TOKEN_REJECTED);
        }

        return $token;
    }

    /**
     * Fetch nonce based on a composite key consisting of the nonce string and a consumer id.
     *
     * @param string $nonce - The nonce string
     * @param int $consumerId - A consumer id
     * @return Mage_Oauth_Model_Nonce
     */
    protected function _getNonce($nonce, $consumerId)
    {
        $nonceObj = $this->_nonceFactory->create()->loadByCompositeKey($nonce, $consumerId);
        return $nonceObj;
    }

    /**
     * Check if token belongs to the same consumer
     *
     * @param $token Mage_Oauth_Model_Token
     * @param $consumer Mage_Oauth_Model_Consumer
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
     * @throws Mage_Oauth_Exception
     */
    protected function _throwException($message = '', $code = 0)
    {
        throw new Mage_Oauth_Exception($message, $code);
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
}
