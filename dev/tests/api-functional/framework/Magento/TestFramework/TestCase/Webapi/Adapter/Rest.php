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

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Webapi\Model\Rest\Config;

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

    /** @var \Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient */
    protected $curlClient;

    /** @var string */
    protected $defaultStoreCode;

    /**
     * Initialize dependencies.
     */
    public function __construct()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $this->_config = $objectManager->get('Magento\Webapi\Model\Config');
        $this->curlClient = $objectManager->get('Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient');
        $this->defaultStoreCode = Bootstrap::getObjectManager()
            ->get('Magento\Framework\StoreManagerInterface')
            ->getStore()
            ->getCode();
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function call($serviceInfo, $arguments = array())
    {
        $resourcePath = '/' . $this->defaultStoreCode . $this->_getRestResourcePath($serviceInfo);
        $httpMethod = $this->_getRestHttpMethod($serviceInfo);
        //Get a valid token
        $accessCredentials = \Magento\TestFramework\Authentication\OauthHelper::getApiAccessCredentials();
        /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
        $oAuthClient = $accessCredentials['oauth_client'];
        $urlFormEncoded = false;
        // we're always using JSON
        $authHeader = array();
        $restServiceInfo = $serviceInfo['rest'];
        if (array_key_exists('token', $restServiceInfo)) {
            $authHeader = $oAuthClient->buildBearerTokenAuthorizationHeader($restServiceInfo['token']);
        } else {
            $authHeader = $oAuthClient->buildOauthAuthorizationHeader(
                $this->curlClient->constructResourceUrl($resourcePath),
                $accessCredentials['key'],
                $accessCredentials['secret'],
                ($httpMethod == 'PUT' || $httpMethod == 'POST') && $urlFormEncoded ? $arguments : array(),
                $httpMethod
            );
        }
        $authHeader = array_merge($authHeader, ['Accept: application/json', 'Content-Type: application/json']);
        switch ($httpMethod) {
            case Config::HTTP_METHOD_GET:
                $response = $this->curlClient->get($resourcePath, array(), $authHeader);
                break;
            case Config::HTTP_METHOD_POST:
                $response = $this->curlClient->post($resourcePath, $arguments, $authHeader);
                break;
            case Config::HTTP_METHOD_PUT:
                $response = $this->curlClient->put($resourcePath, $arguments, $authHeader);
                break;
            case Config::HTTP_METHOD_DELETE:
                $response = $this->curlClient->delete($resourcePath, $authHeader);
                break;
            default:
                throw new \LogicException("HTTP method '{$httpMethod}' is not supported.");
        }
        if (defined('GENERATE_REST_DOCUMENTATION') && GENERATE_REST_DOCUMENTATION) {
            $this->generateDocumentation($httpMethod, $resourcePath, $arguments, $response);
        }

        return $response;
    }

    /**
     * Generate documentation based on request-response data during REST requests.
     *
     * @param string $httpMethod
     * @param string $resourcePath
     * @param array $arguments
     * @param array $response
     */
    protected function generateDocumentation($httpMethod, $resourcePath, $arguments, $response)
    {
        $arguments = json_encode($arguments, JSON_PRETTY_PRINT);
        $response = json_encode($response, JSON_PRETTY_PRINT);
        $varDir = realpath(__DIR__ . '/../../../../../../var');
        $documentationDir = $varDir . '/log/rest-documentation/';
        $debugBackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $pathToFile = $documentationDir;
        foreach ($debugBackTrace as $traceItem) {
            /** Test invocation trace item is the only item which has 3 elements, other trace items have 5 elements */
            if (count($traceItem) == 3) {
                /** Remove 'test' prefix from method name, e.g. testCreate => create */
                $fileName = lcfirst(substr($traceItem['function'], 4));
                /** Remove 'Test' suffix from test class name */
                $pathToFile .= str_replace('\\', '/', substr($traceItem['class'], 0, -4)) . '/';
                break;
            }
        }
        if (!isset($fileName)) {
            $fileName = 'unclassified';
        }
        if (!file_exists($pathToFile)) {
            if (!mkdir($pathToFile, 0755, true)) {
                throw new \RuntimeException('Unable to create missing directory for REST documentation generation');
            }
        }
        $filePath = $pathToFile . $fileName . '.txt';
        if ($resourcePath && $arguments && $response) {
            if (!is_writable(dirname($filePath))) {
                throw new \RuntimeException('Directory for documentation generation is not writable.');
            }
            $resourcePath = urldecode($resourcePath);
            $content = "{$httpMethod} {$resourcePath}\nInput:\n{$arguments}\nOutput:\n{$response}\n\n\n";
            file_put_contents($filePath, $content, FILE_APPEND);
        }
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
