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
use Magento\TestFramework\Helper\Bootstrap;
use OAuth\Common\Consumer\Credentials;
use Magento\TestFramework\Authentication\Rest\OauthClient;

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
        $objectManager = Bootstrap::getObjectManager();
        /** @var $oauthService \Magento\Integration\Service\OauthV1 */
        $oauthService = $objectManager->get('Magento\Integration\Service\OauthV1');

        $url = TESTS_BASE_URL;
        $data = array(
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url
        );

        if (!is_null($date)) {
            $data['created_at'] = $date;
        }

        $consumer = $oauthService->createConsumer($data);
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
     *   'oauth_client' => $oauthClient // OauthClient instance used to fetch the access token
     *   );
     * </pre>
     */
    public static function getAccessToken()
    {
        $consumerCredentials = self::getConsumerCredentials();
        $credentials = new Credentials($consumerCredentials['key'], $consumerCredentials['secret'], TESTS_BASE_URL);
        $oAuthClient = new OauthClient($credentials);
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
     * @param array $resources list of resources to grant to the integration
     * @return array
     * <pre>
     * array (
     *   'key' => 'ajdsjashgdkahsdlkjasldkjals', //token key
     *   'secret' => 'alsjdlaskjdlaksjdlasjkdlas', //token secret
     *   'oauth_client' => $oauthClient // OauthClient instance used to fetch the access token
     *   );
     * </pre>
     * @throws LogicException
     */
    public static function getApiAccessCredentials($resources = null)
    {
        if (!self::$_apiCredentials) {
            $objectManager = Bootstrap::getObjectManager();
            /** @var $integrationService \Magento\Integration\Service\IntegrationV1Interface */
            $integrationService = $objectManager->get('Magento\Integration\Service\IntegrationV1Interface');

            $params = ['name' => 'Integration' . microtime()];

            if ($resources === null || $resources == 'all' ) {
                $params['all_resources'] = true;
            } else {
                $params['resource'] = $resources;
            }

            $integration = $integrationService->create($params);

            /** Magento cache must be cleared to activate just created ACL role. */
            $varPath = realpath('../../../var');
            if (!$varPath) {
                throw new LogicException("Magento cache cannot be cleared after new ACL role creation.");
            } else {
                $cachePath = $varPath . '/cache';
                if (is_dir($cachePath)) {
                    self::_rmRecursive($cachePath);
                }
            }

            /** @var \Magento\Integration\Service\OauthV1 $oauthService */
            $oauthService = $objectManager->get('Magento\Integration\Service\OauthV1');
            $oauthService->createAccessToken($integration->getConsumerId());
            $accessToken = $oauthService->getAccessToken($integration->getConsumerId());
            if (!$accessToken) {
                throw new LogicException('Access token was not created.');
            }
            $consumer = $oauthService->loadConsumer($integration->getConsumerId());
            $credentials = new Credentials(
                $consumer->getKey(), $consumer->getSecret(), TESTS_BASE_URL);
            /** @var $oAuthClient OauthClient */
            $oAuthClient = new OauthClient($credentials);

            self::$_apiCredentials = array(
                'key' => $accessToken->getToken(),
                'secret' => $accessToken->getSecret(),
                'oauth_client' => $oAuthClient
            );
        }
        return self::$_apiCredentials;
    }

    /**
     * Forget API access credentials.
     */
    public static function clearApiAccessCredentials()
    {
        self::$_apiCredentials = false;
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
