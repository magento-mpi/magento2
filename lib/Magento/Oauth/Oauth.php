<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Oauth;

class Oauth implements \Magento\Oauth\OauthInterface
{
    /**
     * Possible time deviation for timestamp validation in sec.
     */
    const TIME_DEVIATION = 600;

    /** @var  \Magento\Oauth\Model\Consumer\Factory */
    private $_consumerFactory;

    /** @var  \Magento\Oauth\Model\Nonce\Factory */
    private $_nonceFactory;

    /** @var  \Magento\Oauth\Model\Token\Factory */
    private $_tokenFactory;

    /** @var  \Magento\Oauth\Helper\Oauth */
    protected $_oauthHelper;

    /** @var  \Magento\Oauth\Helper\Data */
    protected $_dataHelper;

    /** @var  \Zend_Oauth_Http_Utility */
    protected $_httpUtility;

    /** @var \Magento\Core\Model\Date */
    protected $_date;

    /**
     * @param \Magento\Oauth\Model\Consumer\Factory $consumerFactory
     * @param \Magento\Oauth\Model\Nonce\Factory $nonceFactory
     * @param \Magento\Oauth\Model\Token\Factory $tokenFactory
     * @param \Magento\Oauth\Helper\Oauth $oauthHelper
     * @param \Magento\Oauth\Helper\Data $dataHelper
     * @param \Zend_Oauth_Http_Utility $httpUtility
     * @param \Magento\Core\Model\Date $date
     */
    public function __construct(
        \Magento\Oauth\Model\Consumer\Factory $consumerFactory,
        \Magento\Oauth\Model\Nonce\Factory $nonceFactory,
        \Magento\Oauth\Model\Token\Factory $tokenFactory,
        \Magento\Oauth\Helper\Oauth $oauthHelper,
        \Magento\Oauth\Helper\Data $dataHelper,
        \Zend_Oauth_Http_Utility $httpUtility,
        \Magento\Core\Model\Date $date
    ) {
        $this->_consumerFactory = $consumerFactory;
        $this->_nonceFactory = $nonceFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_oauthHelper = $oauthHelper;
        $this->_dataHelper = $dataHelper;
        $this->_httpUtility = $httpUtility;
        $this->_date = $date;
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
     * {@inheritdoc}
     */
    public function getRequestToken($params, $requestUrl, $httpMethod = 'POST')
    {
        $this->_validateVersionParam($params['oauth_version']);
        $consumer = $this->_getConsumerByKey($params['oauth_consumer_key']);
        // must use consumer within expiration period
        $consumerTS = strtotime($consumer->getCreatedAt());
        $expiry = $this->_dataHelper->getConsumerExpirationPeriod();
        if ($this->_date->timestamp() - $consumerTS > $expiry) {
            throw new \Magento\Oauth\Exception('', self::ERR_CONSUMER_KEY_INVALID);
        }
        $this->_validateNonce($params['oauth_nonce'], $consumer->getId(), $params['oauth_timestamp']);
        $token = $this->_getTokenByConsumer($consumer->getId());
        if ($token->getType() != \Magento\Oauth\Model\Token::TYPE_VERIFIER) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }
        $this->_validateSignature(
            $params,
            $consumer->getSecret(),
            $httpMethod,
            $requestUrl
        );
        $requestToken = $token->createRequestToken($token->getId(), $consumer->getCallBackUrl());
        return array('oauth_token' => $requestToken->getToken(), 'oauth_token_secret' => $requestToken->getSecret());
    }

    /**
     * TODO: log the request token in dev mode since its not persisted
     *
     * {@inheritdoc}
     */
    public function getAccessToken($params, $requestUrl, $httpMethod = 'POST')
    {
        $required = array(
            'oauth_consumer_key',
            'oauth_signature',
            'oauth_signature_method',
            'oauth_nonce',
            'oauth_timestamp',
            'oauth_token',
            'oauth_verifier'
        );

        // Make generic validation of request parameters
        $this->_validateProtocolParams($params, $required);

        $oauthToken = $params['oauth_token'];
        $consumerKeyParam = $params['oauth_consumer_key'];

        $consumer = $this->_getConsumerByKey($consumerKeyParam);
        $token = $this->_getToken($oauthToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }

        // The pre-auth token has a value of "request" in the type when it is requested and created initially.
        // In this flow (token flow) the token has to be of type "request" else its marked as reused.
        if (\Magento\Oauth\Model\Token::TYPE_REQUEST != $token->getType()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_USED);
        }

        $this->_validateVerifierParam($params['oauth_verifier'], $token->getVerifier());

        $this->_validateSignature(
            $params,
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
    public function validateAccessTokenRequest($params, $requestUrl, $httpMethod = 'POST')
    {
        $required = array(
            'oauth_consumer_key',
            'oauth_signature',
            'oauth_signature_method',
            'oauth_nonce',
            'oauth_timestamp',
            'oauth_token'
        );

        // make generic validation of request parameters
        $this->_validateProtocolParams($params, $required);

        $oauthToken = $params['oauth_token'];
        $consumerKey = $params['oauth_consumer_key'];

        $consumer = $this->_getConsumerByKey($consumerKey);
        $token = $this->_getToken($oauthToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }
        if (\Magento\Oauth\Model\Token::TYPE_ACCESS != $token->getType()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }
        if ($token->getRevoked()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REVOKED);
        }

        $this->_validateSignature(
            $params,
            $consumer->getSecret(),
            $httpMethod,
            $requestUrl,
            $token->getSecret()
        );

        // If no exceptions were raised return as a valid token
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAccessToken($accessToken)
    {
        $token = $this->_getToken($accessToken);

        // Make sure a consumer is associated with the token
        $this->_getConsumer($token->getConsumerId());

        if (\Magento\Oauth\Model\Token::TYPE_ACCESS != $token->getType()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }
        if ($token->getRevoked()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REVOKED);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function buildAuthorizationHeader(
        $params, $requestUrl, $signatureMethod = self::SIGNATURE_SHA1, $httpMethod = 'POST'
    ) {
        $required = array(
            "oauth_consumer_key",
            "oauth_consumer_secret",
            "oauth_token",
            "oauth_token_secret"
        );
        $this->_checkRequiredParams($params, $required);
        $headerParameters = array(
            'oauth_nonce' => $this->_oauthHelper->generateNonce(),
            'oauth_timestamp' => $this->_date->timestamp(),
            'oauth_version' => '1.0',
        );
        $headerParameters = array_merge($headerParameters, $params);
        $headerParameters['oauth_signature'] = $this->_httpUtility->sign(
            $params,
            $signatureMethod,
            $headerParameters['oauth_consumer_secret'],
            $headerParameters['oauth_token_secret'],
            $httpMethod,
            $requestUrl
        );
        $authorizationHeader = $this->_httpUtility->toAuthorizationHeader($headerParameters);
        // toAuthorizationHeader adds an optional realm="" which is not required for now.
        // http://tools.ietf.org/html/rfc2617#section-1.2
        return str_replace('realm="",', '', $authorizationHeader);
    }

    /**
     * Validate (oauth_nonce) Nonce string.
     *
     * @param string $nonce - Nonce string
     * @param int $consumerId - Consumer Id (Entity Id)
     * @param string|int $timestamp - Unix timestamp
     * @throws \Magento\Oauth\Exception
     */
    protected function _validateNonce($nonce, $consumerId, $timestamp)
    {
        try {
            $timestamp = (int)$timestamp;
            if ($timestamp <= 0 || $timestamp > (time() + self::TIME_DEVIATION)) {
                throw new \Magento\Oauth\Exception(
                    __('Incorrect timestamp value in the oauth_timestamp parameter.'),
                    self::ERR_TIMESTAMP_REFUSED
                );
            }

            $nonceObj = $this->_getNonce($nonce, $consumerId);

            if ($nonceObj->getConsumerId()) {
                throw new \Magento\Oauth\Exception(
                    __('The nonce is already being used by the consumer with id %1.', $consumerId),
                    self::ERR_NONCE_USED
                );
            }

            $consumer = $this->_getConsumer($consumerId);

            if ($nonceObj->getTimestamp() == $timestamp) {
                throw new \Magento\Oauth\Exception(
                    __('The nonce/timestamp combination has already been used.'),
                    self::ERR_NONCE_USED);
            }

            $nonceObj->setNonce($nonce)
                ->setConsumerId($consumer->getId())
                ->setTimestamp($timestamp)
                ->save();
        } catch (\Magento\Oauth\Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new \Magento\Oauth\Exception(__('An error occurred validating the nonce.'));
        }
    }

    /**
     * Validate 'oauth_verifier' parameter
     *
     * @param string $verifier
     * @param string $verifierFromToken
     * @throws \Magento\Oauth\Exception
     */
    protected function _validateVerifierParam($verifier, $verifierFromToken)
    {
        if (!is_string($verifier)) {
            throw new \Magento\Oauth\Exception('', self::ERR_VERIFIER_INVALID);
        }
        if (strlen($verifier) != \Magento\Oauth\Model\Token::LENGTH_VERIFIER) {
            throw new \Magento\Oauth\Exception('', self::ERR_VERIFIER_INVALID);
        }
        if ($verifierFromToken != $verifier) {
            throw new \Magento\Oauth\Exception('', self::ERR_VERIFIER_INVALID);
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
     * @throws \Magento\Oauth\Exception
     */
    protected function _validateSignature($params, $consumerSecret, $httpMethod, $requestUrl, $tokenSecret = null)
    {
        if (!in_array($params['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            throw new \Magento\Oauth\Exception('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        $allowedSignParams = $params;
        // unset unused signature parameters
        unset($allowedSignParams['oauth_signature']);
        unset($allowedSignParams['http_method']);
        unset($allowedSignParams['request_url']);

        $calculatedSign = $this->_httpUtility->sign(
            $allowedSignParams,
            $params['oauth_signature_method'],
            $consumerSecret,
            $tokenSecret,
            $httpMethod,
            $requestUrl
        );

        if ($calculatedSign != $params['oauth_signature']) {
            throw new \Magento\Oauth\Exception('Invalid signature.', self::ERR_SIGNATURE_INVALID);
        }
    }

    /**
     * Validate oauth version
     *
     * @param string $version
     * @throws \Magento\Oauth\Exception
     */
    protected function _validateVersionParam($version)
    {
        // validate version if specified
        if ('1.0' != $version) {
            throw new \Magento\Oauth\Exception('', self::ERR_VERSION_REJECTED);
        }
    }

    /**
     * Validate request and header parameters
     *
     * @param $protocolParams
     * @param $requiredParams
     * @throws \Magento\Oauth\Exception
     */
    protected function _validateProtocolParams($protocolParams, $requiredParams)
    {
        // validate version if specified
        if (isset($protocolParams['oauth_version']) && '1.0' != $protocolParams['oauth_version']) {
            throw new \Magento\Oauth\Exception('', self::ERR_VERSION_REJECTED);
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

        if (isset($protocolParams['oauth_token']) &&
            strlen($protocolParams['oauth_token']) != \Magento\Oauth\Model\Token::LENGTH_TOKEN
        ) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }

        // validate signature method
        if (!in_array($protocolParams['oauth_signature_method'], self::getSupportedSignatureMethods())) {
            throw new \Magento\Oauth\Exception('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }

        $consumer = $this->_getConsumerByKey($protocolParams['oauth_consumer_key']);

        $this->_validateNonce($protocolParams['oauth_nonce'], $consumer->getId(), $protocolParams['oauth_timestamp']);
    }

    /**
     * Get consumer by consumer_id
     *
     * @param $consumerId
     * @return \Magento\Oauth\Model\Consumer
     * @throws \Magento\Oauth\Exception
     */
    protected function _getConsumer($consumerId)
    {
        $consumer = $this->_consumerFactory->create()->load($consumerId);

        if (!$consumer->getId()) {
            throw new \Magento\Oauth\Exception('', self::ERR_PARAMETER_REJECTED);
        }

        return $consumer;
    }

    /**
     * Get a consumer from its key
     *
     * @param string $consumerKey to load
     * @return \Magento\Oauth\Model\Consumer
     * @throws \Magento\Oauth\Exception
     */
    protected function _getConsumerByKey($consumerKey)
    {
        if (strlen($consumerKey) != \Magento\Oauth\Model\Consumer::KEY_LENGTH) {
            throw new \Magento\Oauth\Exception('', self::ERR_CONSUMER_KEY_REJECTED);
        }

        $consumer = $this->_consumerFactory->create()->loadByKey($consumerKey);

        if (!$consumer->getId()) {
            throw new \Magento\Oauth\Exception('', self::ERR_CONSUMER_KEY_REJECTED);
        }

        return $consumer;
    }

    /**
     * Load token object, validate it depending on request type, set access data and save
     *
     * @param string $token
     * @return \Magento\Oauth\Model\Token
     * @throws \Magento\Oauth\Exception
     */
    protected function _getToken($token)
    {
        if (strlen($token) != \Magento\Oauth\Model\Token::LENGTH_TOKEN) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }

        $tokenObj = $this->_tokenFactory->create()->load($token, 'token');

        if (!$tokenObj->getId()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }

        return $tokenObj;
    }

    /**
     * Load token object given a consumer id
     *
     * @param int $consumerId - The id of the consumer
     * @return \Magento\Oauth\Model\Token
     * @throws \Magento\Oauth\Exception
     */
    protected function _getTokenByConsumer($consumerId)
    {
        $token = $this->_tokenFactory->create()->load($consumerId, 'consumer_id');

        if (!$token->getId()) {
            throw new \Magento\Oauth\Exception('', self::ERR_TOKEN_REJECTED);
        }

        return $token;
    }

    /**
     * Fetch nonce based on a composite key consisting of the nonce string and a consumer id.
     *
     * @param string $nonce - The nonce string
     * @param int $consumerId - A consumer id
     * @return \Magento\Oauth\Model\Nonce
     */
    protected function _getNonce($nonce, $consumerId)
    {
        $nonceObj = $this->_nonceFactory->create()->loadByCompositeKey($nonce, $consumerId);
        return $nonceObj;
    }

    /**
     * Check if token belongs to the same consumer
     *
     * @param $token \Magento\Oauth\Model\Token
     * @param $consumer \Magento\Oauth\Model\Consumer
     * @return boolean
     */
    protected function _isTokenAssociatedToConsumer($token, $consumer)
    {
        return $token->getConsumerId() == $consumer->getId();
    }

    /**
     * Check if mandatory OAuth parameters are present
     *
     * @param $protocolParams
     * @param $requiredParams
     * @return mixed
     * @throws \Magento\Oauth\Exception
     */
    protected function _checkRequiredParams($protocolParams, $requiredParams)
    {
        foreach ($requiredParams as $param) {
            if (!isset($protocolParams[$param])) {
                throw new \Magento\Oauth\Exception($param, self::ERR_PARAMETER_ABSENT);
            }
        }
    }
}
