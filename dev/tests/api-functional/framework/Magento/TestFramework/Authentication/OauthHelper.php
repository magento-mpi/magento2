<?php
/**
 * Helper class for generating OAuth related credentials
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Authentication;

class OauthHelper
{
    /**
     * Generate authentication credentials
     * @param string $date consumer creation date
     * @return array
     * <pre>
     * array (
     *   'key' => 'ajdsjashgdkahsdlkjasldkjals', //consumer key
     *   'secret' => 'alsjdlaskjdlaksjdlasjkdlas', //consumer secret
     *   'verifier' => 'oiudioqueoiquweoiqwueoqwuii'
     *   'consumer' => $consumer, // retrieved consumer Model
     *   'token' => $token // retrieved token Model
     *   );
     * </pre>
     */
    public static function getConsumerCredentials($date = null)
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $oauthService \Magento\Oauth\Service\OauthV1 */
        $oauthService = $objectManager->get('Magento\Oauth\Service\OauthV1');
        /** @var $oauthHelper \Magento\Oauth\Helper\Service */
        $oauthHelper = $objectManager->get('Magento\Oauth\Helper\Service');

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
        /** @var  $token \Magento\Oauth\Model\Token */
        $consumer = $objectManager->get('Magento\Oauth\Model\Consumer')
            ->load($consumerData['key'], 'key');
        $token = $objectManager->create('Magento\Oauth\Model\Token');
        $verifier = $token->createVerifierToken($consumer->getId())->getVerifier();

        return array (
            'key' => $consumerKey,
            'secret' => $consumerSecret,
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
     *   'key' => 'ajdsjashgdkahsdlkjasldkjals', //token key
     *   'secret' => 'alsjdlaskjdlaksjdlasjkdlas', //token secret
     *   'oauth_client' => $oauthClient // \Magento\TestFramework\Authentication\Rest\OauthClient instance used to fetch
     *                                      the access token
     *   );
     * </pre>
     */
    public static function getAccessToken()
    {
        $consumerCredentials = self::getConsumerCredentials();
        $credentials = new OAuth\Common\Consumer\Credentials(
            $consumerCredentials['key'], $consumerCredentials['secret'], TESTS_BASE_URL);
        /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
        $oAuthClient = new \Magento\TestFramework\Authentication\Rest\OauthClient($credentials);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            $consumerCredentials['verifier'],
            $requestToken->getRequestTokenSecret()
        );

        return array (
            'key' => $accessToken->getAccessToken(),
            'secret' => $accessToken->getAccessTokenSecret(),
            'oauth_client' => $oAuthClient
        );
    }

}
