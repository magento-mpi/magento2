<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth\Token;

interface ProviderInterface
{
    /**
     * Validate the consumer.
     *
     * @param string $consumerKey - The 'oauth_consumer_key' value.
     * @return \Magento\Oauth\ConsumerInterface - The consumer specified by key.
     * @throws \Magento\Oauth\Exception - Validation errors.
     */
    public function validateConsumer($consumerKey);

    /**
     * Create a request token for the specified consumer.
     *
     * @param \Magento\Oauth\ConsumerInterface $consumer
     * @return array - The request token and secret.
     * <pre>
     *     array(
     *         'oauth_token' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf,
     *         'oauth_token_secret' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf'
     *     )
     * </pre>
     * @throws \Magento\Oauth\Exception - Validation errors.
     */
    public function createRequestToken($consumer);

    /**
     * Validates the request token and verifier. Verifies the request token is associated with the consumer.
     *
     * @param string $requestToken - The 'oauth_token' request token value.
     * @param string $consumerKey - The 'oauth_consumer_key' of the consumer.
     * @param string $oauthVerifier - The 'oauth_verifier' value.
     * @return array - The consumer secret and request token secret.
     * <pre>
     *     array(
     *         'oauth_consumer_secret' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf,
     *         'oauth_token_secret' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf'
     *     )
     * </pre>
     * @throws \Magento\Oauth\Exception - Validation errors.
     */
    public function validateRequestToken($requestToken, $consumerKey, $oauthVerifier);

    /**
     * Retrieve access token for the specified consumer given the consumer key.
     *
     * @param string $consumerKey - The 'oauth_consumer_key' value.
     * @return array - The access token and secret.
     * <pre>
     *     array(
     *         'oauth_token' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf,
     *         'oauth_token_secret' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf'
     *     )
     * </pre>
     * @throws \Magento\Oauth\Exception - Validation errors.
     */
    public function getAccessToken($consumerKey);

    /**
     * Validates the Oauth token type and verifies that it's associated with the consumer.
     *
     * @param string $accessToken - The 'oauth_token' access token value.
     * @param string $consumerKey - The 'oauth_consumer_key' value.
     * @return array - The consumer secret and access token secret.
     * <pre>
     *     array(
     *         'oauth_consumer_secret' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf,
     *         'oauth_token_secret' => 'gshsjkndtyhwjhdbutfgbsnhtrequikf'
     *     )
     * </pre>
     * @throws \Magento\Oauth\Exception - Validation errors.
     */
    public function validateAccessTokenRequest($accessToken, $consumerKey);

    /**
     * Validate an access token string.
     *
     * @param string - The 'oauth_token' access token string.
     * @return bool - True if the access token is valid.
     * @throws \Magento\Oauth\Exception - Validation errors.
     */
    public function validateAccessToken($accessToken);

    /**
     * Perform basic validation of an Oauth token, of any type (e.g. request, access, etc.).
     *
     * @param string $oauthToken - The token string.
     * @return bool - True if the Oauth token passes basic validation.
     */
    public function validateOauthToken($oauthToken);

    /**
     * Retrieve a consumer given the consumer's key.
     *
     * @param string $consumerKey - The 'oauth_consumer_key' value.
     * @return \Magento\Oauth\ConsumerInterface
     * @throws \Magento\Oauth\Exception
     */
    public function getConsumerByKey($consumerKey);
}
