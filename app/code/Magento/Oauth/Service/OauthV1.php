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
 * Class Magento_Oauth_Service_OauthV1
 */
class Magento_Oauth_Service_OauthV1 implements Magento_Oauth_Service_OauthV1Interface
{
    /**
     * Possible time deviation for timestamp validation in sec.
     */
    const TIME_DEVIATION = 600;

    /** @var  Magento_Oauth_Model_Consumer_Factory */
    private $_consumerFactory;

    /** @var  Magento_Oauth_Model_Nonce_Factory */
    private $_nonceFactory;

    /** @var  Magento_Oauth_Model_Token_Factory */
    private $_tokenFactory;

    /** @var  Magento_Oauth_Helper_Data */
    protected $_helperData;

    /** @var  Magento_Core_Model_StoreManagerInterface */
    protected $_storeManager;

    /** @var  Magento_HTTP_ZendClient */
    protected $_httpClient;

    /**
     * @param Magento_Oauth_Model_Consumer_Factory $consumerFactory
     * @param Magento_Oauth_Model_Nonce_Factory $nonceFactory
     * @param Magento_Oauth_Model_Token_Factory $tokenFactory
     * @param Magento_Oauth_Helper_Data $helperData
     * @param Magento_Core_Model_StoreManagerInterface
     * @param Magento_HTTP_ZendClient
     */
    public function __construct(
        Magento_Oauth_Model_Consumer_Factory $consumerFactory,
        Magento_Oauth_Model_Nonce_Factory $nonceFactory,
        Magento_Oauth_Model_Token_Factory $tokenFactory,
        Magento_Oauth_Helper_Data $helperData,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_HTTP_ZendClient $httpClient
    ) {
        $this->_consumerFactory = $consumerFactory;
        $this->_nonceFactory = $nonceFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_store = $storeManager->getStore();
        $this->_helperData = $helperData;
        $this->_httpClient = $httpClient;
    }

    /**
     * Retrieve array of supported signature methods.
     *
     * @return array - Supported HMAC-SHA1 and HMAC-SHA256 signature methods.
     */
    public static function getSupportedSignatureMethods()
    {
        return array(Magento_Oauth_Helper_Data::SIGNATURE_SHA1, Magento_Oauth_Helper_Data::SIGNATURE_SHA256);
    }

    /**
     * {@inheritdoc}
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
            $this->_throwException(__('Unexpected error. Unable to create OAuth Consumer account.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postToConsumer($request)
    {
        try {
            $consumerData = $this->_getConsumer($request['consumer_id'])->getData();
            $storeUrl = $this->_store->getBaseUrl();
            $this->_httpClient->setUri($consumerData['http_post_url']);
            $this->_httpClient->setParameterPost(array(
                'oauth_consumer_key' => $consumerData['key'],
                'oauth_consumer_secret' => $consumerData['secret'],
                'store_url' => $storeUrl
            ));
            // TODO: Uncomment this when there is a live http_post_url that we can actually post to.
            //$this->_httpClient->request(Magento_HTTP_ZendClient::POST);

            $verifier = $this->_tokenFactory->create()->createVerifierToken($request['consumer_id']);
            return array('oauth_verifier' => $verifier->getVerifier());
        } catch (Magento_Core_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            $this->_throwException(__('Unexpected error. Unable to post data to consumer.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestToken($signedRequest)
    {
        $this->_validateVersionParam($signedRequest['oauth_version']);

        $consumer = $this->_getConsumerByKey($signedRequest['oauth_consumer_key']);

        // must use consumer within expiration period

        $consumerTS = strtotime($consumer->getCreatedAt());
        if (time() - $consumerTS > $this->_helperData->getConsumerExpirationPeriod()) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_CONSUMER_KEY_INVALID);
        }

        $this->_validateNonce($signedRequest['nonce'], $consumer->getId(), $signedRequest['oauth_timestamp']);

        $token = $this->_getTokenByConsumer($consumer->getId());

        if ($token->getType() != Magento_Oauth_Model_Token::TYPE_VERIFIER) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
        }

        //OAuth clients are not sending the verifier param for requestToken requests
        //$this->_validateVerifierParam($signedRequest['oauth_verifier'], $token->getVerifier());

        $this->_validateSignature(
            $signedRequest,
            $consumer->getSecret(),
            $signedRequest['http_method'],
            $signedRequest['request_url']
        );

        $requestToken = $token->createRequestToken($token->getId(), $consumer->getCallBackUrl());
        return array('oauth_token' => $requestToken->getToken(), 'oauth_token_secret' => $requestToken->getSecret());
    }

    /**
     * TODO: log the request token in dev mode since its not persisted
     *
     * {@inheritdoc}
     */
    public function getAccessToken($request)
    {
        $required = array(
            "oauth_consumer_key",
            "oauth_signature",
            "oauth_signature_method",
            "oauth_nonce",
            "oauth_timestamp",
            "oauth_token",
            "oauth_verifier"
        );

        // Make generic validation of request parameters
        $this->_validateProtocolParams($request, $required);

        $oauthToken = $request['oauth_token'];
        $requestUrl = $request['request_url'];
        $httpMethod = $request['http_method'];
        $consumerKeyParam = $request['oauth_consumer_key'];

        $consumer = $this->_getConsumerByKey($consumerKeyParam);
        $token = $this->_getToken($oauthToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
        }

        // The pre-auth token has a value of "request" in the type when it is requested and created initially.
        // In this flow (token flow) the token has to be of type "request" else its marked as reused.
        // TODO: Need to check security implication of this message
        if (Magento_Oauth_Model_Token::TYPE_REQUEST != $token->getType()) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_USED);
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
     * {@inheritdoc}
     */
    public function validateAccessToken($request)
    {
        $required = array(
            "oauth_consumer_key",
            "oauth_signature",
            "oauth_signature_method",
            "oauth_nonce",
            "oauth_timestamp",
            "oauth_token"
        );

        $oauthToken = $request['oauth_token'];
        $requestUrl = $request['request_url'];
        $httpMethod = $request['http_method'];
        $consumerKey = $request['oauth_consumer_key'];

        // make generic validation of request parameters
        $this->_validateProtocolParams($request, $required);

        $consumer = $this->_getConsumerByKey($consumerKey);
        $token = $this->_getToken($oauthToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
        }

        if (Magento_Oauth_Model_Token::TYPE_ACCESS != $token->getType()) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
        }
        if ($token->getRevoked()) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REVOKED);
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
                $this->_throwException(
                    __('Incorrect timestamp value in the oauth_timestamp parameter.'),
                    Magento_Oauth_Helper_Data::ERR_TIMESTAMP_REFUSED
                );
            }

            $nonceObj = $this->_getNonce($nonce, $consumerId);

            if ($nonceObj->getConsumerId()) {
                $this->_throwException(
                    __('The nonce is already being used by the consumer with id %1.', $consumerId),
                    Magento_Oauth_Helper_Data::ERR_NONCE_USED
                );
            }

            $consumer = $this->_getConsumer($consumerId);

            if ($nonceObj->getTimestamp() == $timestamp) {
                $this->_throwException(
                    __('The nonce/timestamp combination has already been used.'),
                    Magento_Oauth_Helper_Data::ERR_NONCE_USED);
            }

            $nonceObj->setNonce($nonce)
                ->setConsumerId($consumer->getId())
                ->setTimestamp($timestamp)
                ->save();
        } catch (Magento_Oauth_Exception $exception) {
            throw $exception;
        } catch (Exception $exception) {
            $this->_throwException(__('An error occurred validating the nonce.'));
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_VERIFIER_INVALID);
        }
        if (strlen($verifier) != Magento_Oauth_Model_Token::LENGTH_VERIFIER) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_VERIFIER_INVALID);
        }
        if ($verifierFromToken != $verifier) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_VERIFIER_INVALID);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_SIGNATURE_METHOD_REJECTED);
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
            $this->_throwException('Invalid signature.', Magento_Oauth_Helper_Data::ERR_SIGNATURE_INVALID);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_VERSION_REJECTED);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_VERSION_REJECTED);
        }
        // required parameters validation. Default to minimum required params if not provided
        if (empty($requiredParams)) {
            $requiredParams = array(
                "oauth_consumer_key",
                "oauth_signature",
                "oauth_signature_method",
                "oauth_nonce",
                "oauth_timestamp"
            );
        }
        $this->_checkRequiredParams($protocolParams, $requiredParams);

        if (isset($protocolParams['oauth_token']) && strlen(
                $protocolParams['oauth_token']
            ) != Magento_Oauth_Model_Token::LENGTH_TOKEN
        ) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
        }

        // validate parameters type
        //TODO Need to verify if this is required
        foreach ($protocolParams as $paramName => $paramValue) {
            if (!is_string($paramValue)) {
                $this->_throwException($paramName, Magento_Oauth_Helper_Data::ERR_PARAMETER_REJECTED);
            }
        }
        // validate signature method
        if (!in_array($protocolParams['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_SIGNATURE_METHOD_REJECTED);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_PARAMETER_REJECTED);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_CONSUMER_KEY_REJECTED);
        }

        $consumer = $this->_consumerFactory->create()->loadByKey($consumerKey);

        if (!$consumer->getId()) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_CONSUMER_KEY_REJECTED);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
        }

        $tokenObj = $this->_tokenFactory->create()->load($token, 'token');

        if (!$tokenObj->getId()) {
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
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
            $this->_throwException('', Magento_Oauth_Helper_Data::ERR_TOKEN_REJECTED);
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
                $this->_throwException($param, Magento_Oauth_Helper_Data::ERR_PARAMETER_ABSENT);
            }
        }
    }
}
