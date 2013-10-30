<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Oauth\Model\Token;

use Magento\Oauth\OauthInterface;
use Magento\Oauth\Token\ProviderInterface;

class Provider implements ProviderInterface
{
    /** @var \Magento\Oauth\Model\Consumer\Factory */
    protected $_consumerFactory;

    /** @var \Magento\Oauth\Model\Token\Factory */
    protected $_tokenFactory;

    /** @var  \Magento\Oauth\Helper\Data */
    protected $_dataHelper;

    /** @var \Magento\Core\Model\Date */
    protected $_date;

    /**
     * @param \Magento\Oauth\Model\Consumer\Factory $consumerFactory
     * @param \Magento\Oauth\Model\Token\Factory $tokenFactory
     * @param \Magento\Oauth\Helper\Data $dataHelper
     * @param \Magento\Core\Model\Date $date
     */
    public function __construct(
        \Magento\Oauth\Model\Consumer\Factory $consumerFactory,
        \Magento\Oauth\Model\Token\Factory $tokenFactory,
        \Magento\Oauth\Helper\Data $dataHelper,
        \Magento\Core\Model\Date $date
    ) {
        $this->_consumerFactory = $consumerFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_dataHelper = $dataHelper;
        $this->_date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConsumer($consumerKey)
    {
        $consumer = $this->getConsumerByKey($consumerKey);
        // Must use consumer within expiration period.
        $consumerTS = strtotime($consumer->getCreatedAt());
        $expiry = $this->_dataHelper->getConsumerExpirationPeriod();
        if ($this->_date->timestamp() - $consumerTS > $expiry) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_CONSUMER_KEY_INVALID);
        }
        return $consumer;
    }

    /**
     * {@inheritdoc}
     */
    public function createRequestToken($consumer)
    {
        $token = $this->_getTokenByConsumer($consumer->getId());
        if ($token->getType() != \Magento\Oauth\Model\Token::TYPE_VERIFIER) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }
        $requestToken = $token->createRequestToken($token->getId(), $consumer->getCallbackUrl());
        return array('oauth_token' => $requestToken->getToken(), 'oauth_token_secret' => $requestToken->getSecret());
    }

    /**
     * {@inheritdoc}
     */
    public function validateRequestToken($requestToken, $consumerKey, $oauthVerifier)
    {
        $consumer = $this->getConsumerByKey($consumerKey);
        $token = $this->_getToken($requestToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }

        // The pre-auth token has a value of "request" in the type when it is requested and created initially.
        // In this flow (token flow) the token has to be of type "request" else its marked as reused.
        if (\Magento\Oauth\Model\Token::TYPE_REQUEST != $token->getType()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_USED);
        }

        $this->_validateVerifierParam($oauthVerifier, $token->getVerifier());

        return array(
            'oauth_consumer_secret' => $consumer->getSecret(), 'oauth_token_secret' => $token->getSecret()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken($consumerKey)
    {
        $consumer = $this->getConsumerByKey($consumerKey);
        /** TODO: log the request token in dev mode since its not persisted. */
        $token = $this->_getTokenByConsumer($consumer->getId());
        if (\Magento\Oauth\Model\Token::TYPE_REQUEST != $token->getType()) {
            throw new \Magento\Oauth\Exception('Cannot convert due to token is not request type');
        }
        $accessToken = $token->convertToAccess();
        return array('oauth_token' => $accessToken->getToken(), 'oauth_token_secret' => $accessToken->getSecret());
    }

    /**
     * {@inheritdoc}
     */
    public function validateAccessTokenRequest($accessToken, $consumerKey)
    {
        $consumer = $this->getConsumerByKey($consumerKey);
        $token = $this->_getToken($accessToken);

        if (!$this->_isTokenAssociatedToConsumer($token, $consumer)) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }
        if (\Magento\Oauth\Model\Token::TYPE_ACCESS != $token->getType()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }
        if ($token->getRevoked()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REVOKED);
        }

        return array(
            'oauth_consumer_secret' => $consumer->getSecret(), 'oauth_token_secret' => $token->getSecret()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateAccessToken($accessToken)
    {
        $token = $this->_getToken($accessToken);
        // Make sure a consumer is associated with the token.
        $this->_getConsumer($token->getConsumerId());

        if (\Magento\Oauth\Model\Token::TYPE_ACCESS != $token->getType()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }

        if ($token->getRevoked()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REVOKED);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateOauthToken($oauthToken)
    {
        return strlen($oauthToken) == \Magento\Oauth\Model\Token::LENGTH_TOKEN;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumerByKey($consumerKey)
    {
        if (strlen($consumerKey) != \Magento\Oauth\Model\Consumer::KEY_LENGTH) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_CONSUMER_KEY_REJECTED);
        }

        $consumer = $this->_consumerFactory->create()->loadByKey($consumerKey);

        if (!$consumer->getId()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_CONSUMER_KEY_REJECTED);
        }

        return $consumer;
    }

    /**
     * Validate 'oauth_verifier' parameter.
     *
     * @param string $oauthVerifier
     * @param string $tokenVerifier
     * @throws \Magento\Oauth\Exception
     */
    protected function _validateVerifierParam($oauthVerifier, $tokenVerifier)
    {
        if (!is_string($oauthVerifier)) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_VERIFIER_INVALID);
        }
        if (!$this->validateOauthToken($oauthVerifier)) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_VERIFIER_INVALID);
        }
        if ($tokenVerifier != $oauthVerifier) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_VERIFIER_INVALID);
        }
    }

    /**
     * Get consumer by consumer_id.
     *
     * @param $consumerId
     * @return \Magento\Oauth\ConsumerInterface
     * @throws \Magento\Oauth\Exception
     */
    protected function _getConsumer($consumerId)
    {
        $consumer = $this->_consumerFactory->create()->load($consumerId);

        if (!$consumer->getId()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_PARAMETER_REJECTED);
        }

        return $consumer;
    }

    /**
     * Load token object and validate it.
     *
     * @param string $token
     * @return \Magento\Oauth\Model\Token
     * @throws \Magento\Oauth\Exception
     */
    protected function _getToken($token)
    {
        if (!$this->validateOauthToken($token)) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }

        $tokenObj = $this->_tokenFactory->create()->load($token, 'token');

        if (!$tokenObj->getId()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }

        return $tokenObj;
    }

    /**
     * Load token object given a consumer Id.
     *
     * @param int $consumerId - The Id of the consumer.
     * @return \Magento\Oauth\Model\Token
     * @throws \Magento\Oauth\Exception
     */
    protected function _getTokenByConsumer($consumerId)
    {
        $token = $this->_tokenFactory->create()->load($consumerId, 'consumer_id');

        if (!$token->getId()) {
            throw new \Magento\Oauth\Exception('', OauthInterface::ERR_TOKEN_REJECTED);
        }

        return $token;
    }

    /**
     * Check if token belongs to the same consumer.
     *
     * @param $token \Magento\Oauth\Model\Token
     * @param $consumer \Magento\Oauth\ConsumerInterface
     * @return boolean
     */
    protected function _isTokenAssociatedToConsumer($token, $consumer)
    {
        return $token->getConsumerId() == $consumer->getId();
    }
}
