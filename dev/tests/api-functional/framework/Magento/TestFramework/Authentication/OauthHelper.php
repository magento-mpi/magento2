<?php
/**
 * Helper class for generating OAuth related credentials
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_Authentication_OauthHelper
{

    /**
     * Generate authentication credentials
     * @param string $date consumer creation date
     * @return array
     * <pre>
     * array (
     *   'consumer_key' => 'ajdsjashgdkahsdlkjasldkjals',
     *   'consumer_secret' => 'alsjdlaskjdlaksjdlasjkdlas',
     *   'verifier' => 'oiudioqueoiquweoiqwueoqwuii'
     *   'consumer' => $consumer, // retrieved consumer Model
     *   'token' => $token // retrieved token Model
     *   );
     * </pre>
     */
    public static function geConsumerCredentials($date = null)
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $oauthService Magento_Oauth_Service_OauthV1 */
        $oauthService = $objectManager->get('Magento_Oauth_Service_OauthV1');
        /** @var $oauthHelper Magento_Oauth_Helper_Data */
        $oauthHelper = $objectManager->get('Magento_Oauth_Helper_Data');

        $consumerKey = $oauthHelper->generateConsumerKey();
        $consumerSecret = $oauthHelper->generateConsumerSecret();

        $url = TESTS_BASE_URL;
        $data = array(
            'created_at' => is_null($date) ? date('Y-m-d H:i:s') : $date,
            'key' => $consumerKey,
            'secret' => $consumerSecret,
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url,
            'http_post_url' => $url
        );

        /** @var array $consumerData */
        $consumerData = $oauthService->createConsumer($data);
        /** @var  $token Magento_Oauth_Model_Token */
        $consumer = $objectManager->get('Magento_Oauth_Model_Consumer')
            ->load($consumerData['key'], 'key');
        $token = $objectManager->create('Magento_Oauth_Model_Token');
        $verifier = $token->createVerifierToken($consumer->getId())->getVerifier();

        return array (
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret,
            'verifier' => $verifier,
            'consumer' => $consumer,
            'token' => $token
        );
    }

    /**
     * Create an access token to associated to a consumer to access APIs
     * @return array comprising of token  key and secret
     * <pre>
     * array (
     *   'token_key' => 'ajdsjashgdkahsdlkjasldkjals',
     *   'token_secret' => 'alsjdlaskjdlaksjdlasjkdlas',
     *   'token_client' => $oauthClient // Magento_TestFramework_Authentication_Rest_OauthClient instance used to fetch
     *                                      the access token
     *   );
     * </pre>
     */
    public static function getAccessToken()
    {
        $consumerCredentials = self::geConsumerCredentials();
        $credentials = new OAuth\Common\Consumer\Credentials(
            $consumerCredentials['consumer_key'], $consumerCredentials['consumer_secret'], TESTS_BASE_URL);
        /** @var $oAuthClient Magento_TestFramework_Authentication_Rest_OauthClient */
        $oAuthClient = new Magento_TestFramework_Authentication_Rest_OauthClient($credentials);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            $consumerCredentials['verifier'],
            $requestToken->getRequestTokenSecret()
        );

        return array (
            'token_key' => $accessToken->getAccessToken(),
            'token_secret' => $accessToken->getAccessTokenSecret(),
            'oauth_client' => $oAuthClient
        );
    }

}