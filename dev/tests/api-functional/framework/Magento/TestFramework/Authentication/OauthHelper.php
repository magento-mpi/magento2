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

use Zend\Stdlib\Exception\LogicException;

class OauthHelper
{
    /** @var array */
    protected static $_apiCredentials;

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

        $url = TESTS_BASE_URL;
        $data = array(
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url
        );

        if (!is_null($date)) {
            $data['created_at'] = $date;
        }

        $consumer = $consumerHelper->createConsumer($data);
        $token = $objectManager->create('Magento\Integration\Model\Oauth\Token');
        $verifier = $token->createVerifierToken($consumer->getId())->getVerifier();

        return array (
            'key' => $consumer->getKey(),
            'secret' => $consumer->getSecret(),
            'verifier' => $verifier,
            'consumer' => $consumer,
            'token' => $token
        );
    }

    /**
     * Create an access token to associated to a consumer to access APIs. No resources are available to this consumer.
     *
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

        /** TODO: Reconsider return format. It is not aligned with method name. */
        return array (
            'key' => $accessToken->getAccessToken(),
            'secret' => $accessToken->getAccessTokenSecret(),
            'oauth_client' => $oAuthClient
        );
    }

    /**
     * Create an access token, tied to integration which has permissions to all API resources in the system.
     *
     * @return array
     * <pre>
     * array (
     *   'key' => 'ajdsjashgdkahsdlkjasldkjals', //token key
     *   'secret' => 'alsjdlaskjdlaksjdlasjkdlas', //token secret
     *   'oauth_client' => $oauthClient // \Magento\TestFramework\Authentication\Rest\OauthClient instance used to fetch
     *                                      the access token
     *   );
     * </pre>
     * @throws LogicException
     */
    public static function getApiAccessCredentials()
    {
        if (!self::$_apiCredentials) {
            /** @var $objectManager \Magento\TestFramework\ObjectManager */
            $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
            /** @var $integrationService \Magento\Integration\Service\IntegrationV1Interface */
            $integrationService = $objectManager->get('Magento\Integration\Service\IntegrationV1Interface');
            $integrationData = $integrationService->create(
                array(
                    'name' => 'Integration' . microtime(),
                    'all_resources' => true
                )
            );

            /** Magento cache must be cleared to activate just created ACL role. */
            $appCachePath = realpath('../../../var/cache');
            if (!$appCachePath) {
                throw new LogicException("Magento cache cannot be cleared after new ACL role creation.");
            }
            self::_rmRecursive($appCachePath);

            /** @var \Magento\Integration\Helper\Oauth\Consumer $consumerHelper */
            $consumerHelper = $objectManager->get('Magento\Integration\Helper\Oauth\Consumer');
            $consumerHelper->createAccessToken($integrationData['consumer_id']);
            $accessToken = $consumerHelper->getAccessToken($integrationData['consumer_id']);
            if (!$accessToken) {
                throw new LogicException('Access token was not created.');
            }
            $consumer = $consumerHelper->loadConsumer($integrationData['consumer_id']);
            $credentials = new \OAuth\Common\Consumer\Credentials(
                $consumer->getKey(), $consumer->getSecret(), TESTS_BASE_URL);
            /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
            $oAuthClient = new \Magento\TestFramework\Authentication\Rest\OauthClient($credentials);

            self::$_apiCredentials = array(
                'key' => $accessToken->getToken(),
                'secret' => $accessToken->getSecret(),
                'oauth_client' => $oAuthClient
            );
        }
        return self::$_apiCredentials;
    }

    /**
     * Remove fs element with nested elements.
     *
     * @param string $dir
     */
    protected static function _rmRecursive($dir)
    {
        if (is_dir($dir)) {
            foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $object) {
                if (is_dir($object)) {
                    self::_rmRecursive($object);
                } else {
                    unlink($object);
                }
            }
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }
}
