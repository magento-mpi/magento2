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
        /** @var $consumerHelper \Magento\Integration\Helper\Oauth\Consumer */
        $consumerHelper = $objectManager->get('Magento\Integration\Helper\Oauth\Consumer');
        /** @var $oauthHelper \Magento\Oauth\Helper\Oauth */
        $oauthHelper = $objectManager->get('Magento\Oauth\Helper\Oauth');

        $consumerKey = $oauthHelper->generateConsumerKey();
        $consumerSecret = $oauthHelper->generateConsumerSecret();

        $url = TESTS_BASE_URL;
        $data = array(
            'key' => $consumerKey,
            'secret' => $consumerSecret,
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url
        );

        if (!is_null($date)) {
            $data['created_at'] = $date;
        }

        /** @var array $consumerData */
        $consumerData = $consumerHelper->createConsumer($data);
        /** @var  $consumer \Magento\Integration\Model\Oauth\Consumer */
        $consumer = $objectManager->get('Magento\Integration\Model\Oauth\Consumer')
            ->load($consumerData['key'], 'key');
        $token = $objectManager->create('Magento\Integration\Model\Oauth\Token');
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
        $credentials = new \OAuth\Common\Consumer\Credentials(
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
