<?php
/**
 * Test client for REST API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\TestCase\Webapi\Adapter;

use Magento\Webapi\Model\Config\Converter;

class Rest implements \Magento\TestFramework\TestCase\Webapi\AdapterInterface
{
    /** @var \Magento\Webapi\Model\Config */
    protected $_config;

    /** @var \Magento\Integration\Model\Oauth\Consumer */
    protected static $_consumer;

    /** @var \Magento\Integration\Model\Oauth\Token */
    protected static $_token;

    /** @var string */
    protected static $_consumerKey;

    /** @var string */
    protected static $_consumerSecret;

    /** @var string */
    protected static $_verifier;

    /**
     * Initialize dependencies.
     */
    public function __construct()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_config = $objectManager->get('Magento\Webapi\Model\Config');
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function call($serviceInfo, $arguments = array())
    {
        $defaultStoreCode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getStore()->getCode();
        $resourcePath = '/' . $defaultStoreCode . $this->_getRestResourcePath($serviceInfo);
        $httpMethod = $this->_getRestHttpMethod($serviceInfo);
        //Get a valid token
        $accessCredentials = \Magento\TestFramework\Authentication\OauthHelper::getApiAccessCredentials();
        /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
        $oAuthClient = $accessCredentials['oauth_client'];
        // delegate the request to vanilla cURL REST client
        $curlClient = new \Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient();
        $urlFormEncoded = false;
        // we're always using JSON
        $oauthHeader = $oAuthClient->buildOauthHeaderForApiRequest(
            $curlClient->constructResourceUrl($resourcePath),
            $accessCredentials['key'],
            $accessCredentials['secret'],
            ($httpMethod == 'PUT' || $httpMethod == 'POST') && $urlFormEncoded ? $arguments : array(),
            $httpMethod
        );
        switch ($httpMethod) {
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET:
                $response = $curlClient->get($resourcePath, array(), $oauthHeader);
                break;
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST:
                $response = $curlClient->post($resourcePath, $arguments, $oauthHeader);
                break;
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT:
                $response = $curlClient->put($resourcePath, $arguments, $oauthHeader);
                break;
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE:
                $response = $curlClient->delete($resourcePath, $oauthHeader);
                break;
            default:
                throw new \LogicException("HTTP method '{$httpMethod}' is not supported.");
        }
        return $response;
    }

    /**
     * Retrieve REST endpoint from $serviceInfo array and return it to the caller.
     *
     * @param array $serviceInfo
     * @return string
     * @throws \Exception
     */
    protected function _getRestResourcePath($serviceInfo)
    {
        if (isset($serviceInfo['rest']['resourcePath'])) {
            $resourcePath = $serviceInfo['rest']['resourcePath'];
        }
        if (!isset($resourcePath)) {
            throw new \Exception("REST endpoint cannot be identified.");
        }
        return $resourcePath;
    }

    /**
     * Retrieve HTTP method to be used in REST request.
     *
     * @param array $serviceInfo
     * @return string
     * @throws \Exception
     */
    protected function _getRestHttpMethod($serviceInfo)
    {
        if (isset($serviceInfo['rest']['httpMethod'])) {
            $httpMethod = $serviceInfo['rest']['httpMethod'];
        }
        if (!isset($httpMethod)) {
            throw new \Exception("REST HTTP method cannot be identified.");
        }
        return $httpMethod;
    }
}
