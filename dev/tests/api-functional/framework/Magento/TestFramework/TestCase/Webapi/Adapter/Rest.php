<?php
/**
 * Test client for REST API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_TestCase_Webapi_Adapter_Rest
    implements Magento_TestFramework_TestCase_Webapi_AdapterInterface
{
    /** @var Magento_Webapi_Model_Config */
    protected $_config;

    /** @var Magento_Oauth_Model_Consumer */
    protected static $_consumer;

    /** @var Magento_Oauth_Model_Token */
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
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_config = $objectManager->get('Magento_Webapi_Model_Config');
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function call($serviceInfo, $arguments = array())
    {
        $resourcePath = $this->_getRestResourcePath($serviceInfo);
        $httpMethod = $this->_getRestHttpMethod($serviceInfo);
        //Setup Oauth header
        $this->createConsumer();
        $credentials = new OAuth\Common\Consumer\Credentials(
            self::$_consumerKey, self::$_consumerSecret, TESTS_BASE_URL);
        /** @var $oAuthClient Magento_TestFramework_Authentication_Rest_OauthClient */
        $oAuthClient = new Magento_TestFramework_Authentication_Rest_OauthClient($credentials);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            self::$_verifier,
            $requestToken->getRequestTokenSecret()
        );

        // delegate the request to vanilla cURL REST client
        $curlClient = new Magento_TestFramework_TestCase_Webapi_Adapter_Rest_CurlClient();
        $oauthHeader = $oAuthClient
            ->buildOauthHeaderForApiRequest($curlClient->constructResourceUrl($resourcePath),
                                            $accessToken->getAccessToken(),
                                            $accessToken->getAccessTokenSecret(),
                                            ($httpMethod == 'PUT' || $httpMethod == 'POST') ? $arguments : array(),
                                            $httpMethod);
        switch ($httpMethod) {
            case Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET:
                $response = $curlClient->get($resourcePath, array(), $oauthHeader);
                break;
            case Magento_Webapi_Model_Rest_Config::HTTP_METHOD_POST:
                $response = $curlClient->post($resourcePath, $arguments, $oauthHeader);
                break;
            case Magento_Webapi_Model_Rest_Config::HTTP_METHOD_PUT:
                $response = $curlClient->put($resourcePath, $arguments, $oauthHeader);
                break;
            case Magento_Webapi_Model_Rest_Config::HTTP_METHOD_DELETE:
                $response = $curlClient->delete($resourcePath, $oauthHeader);
                break;
            default:
                throw new LogicException("HTTP method '{$httpMethod}' is not supported.");
        }
        if (!is_array($response)) {
            /** Array is defined as the only return type in the adapter interface */
            $responseType = gettype($response);
            throw new RuntimeException("Response type is invalid. Array expected, '{$responseType}' given.");
        }
        return $response;
    }

    /**
     * Create a consumer
     */
    protected function createConsumer()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $oauthService Magento_Oauth_Service_OauthV1 */
        $oauthService = $objectManager->get('Magento_Oauth_Service_OauthV1');
        /** @var $oauthHelper Magento_Oauth_Helper_Data */
        $oauthHelper = $objectManager->get('Magento_Oauth_Helper_Data');

        self::$_consumerKey = $oauthHelper->generateConsumerKey();
        self::$_consumerSecret = $oauthHelper->generateConsumerSecret();

        $url = TESTS_BASE_URL;
        $data = array(
            'created_at' => date('Y-m-d H:i:s'),
            'key' => self::$_consumerKey,
            'secret' => self::$_consumerSecret,
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url,
            'http_post_url' => $url
        );

        /** @var array $consumerData */
        $consumerData = $oauthService->createConsumer($data);
        /** @var  $token Magento_Oauth_Model_Token */
        self::$_consumer = $objectManager->get('Magento_Oauth_Model_Consumer')
            ->load($consumerData['key'], 'key');
        self::$_token = $objectManager->create('Magento_Oauth_Model_Token');
        self::$_verifier = self::$_token->createVerifierToken(self::$_consumer->getId())->getVerifier();
    }

    /**
     * Retrieve REST endpoint from $serviceInfo array and return it to the caller.
     *
     * @param array $serviceInfo
     * @return string
     * @throws Exception
     */
    protected function _getRestResourcePath($serviceInfo)
    {
        if (isset($serviceInfo['rest']['resourcePath'])) {
            $resourcePath = $serviceInfo['rest']['resourcePath'];
        } else if (isset($serviceInfo['serviceInterface']) && isset($serviceInfo['method'])) {
            /** Identify resource path using service interface name and service method name */
            $services = $this->_config->getServices();
            $serviceInterface = $serviceInfo['serviceInterface'];
            $method = $serviceInfo['method'];
            if (isset($services[$serviceInterface]['methods'][$method])) {
                $serviceData = $services[$serviceInterface];
                $methodData = $serviceData['methods'][$method];
                $routePattern = $serviceData[Magento_Webapi_Model_Config::ATTR_SERVICE_PATH] . $methodData['route'];
                $numberOfPlaceholders = substr_count($routePattern, ':');
                if ($numberOfPlaceholders == 1) {
                    if (!isset($serviceInfo['entityId'])) {
                        throw new LogicException('Entity ID is required (to be used instead of placeholder).');
                    }
                    $resourcePath = preg_replace('#:\w+#', $serviceInfo['entityId'], $routePattern);
                } else if ($numberOfPlaceholders > 1) {
                    throw new LogicException("Current implementation of Web API functional framework "
                        . "is able to process only one placeholder in REST route.");
                }
            }
        }
        if (!isset($resourcePath)) {
            throw new Exception("REST endpoint cannot be identified.");
        }
        return $resourcePath;
    }

    /**
     * Retrieve HTTP method to be used in REST request.
     *
     * @param array $serviceInfo
     * @return string
     * @throws Exception
     */
    protected function _getRestHttpMethod($serviceInfo)
    {
        if (isset($serviceInfo['rest']['httpMethod'])) {
            $httpMethod = $serviceInfo['rest']['httpMethod'];
        } else if (isset($serviceInfo['serviceInterface']) && isset($serviceInfo['method'])) {
            /** Identify HTTP method using service interface name and service method name */
            $services = $this->_config->getServices();
            $serviceInterface = $serviceInfo['serviceInterface'];
            $method = $serviceInfo['method'];
            if (isset($services[$serviceInterface]['methods'][$method])) {
                $httpMethod
                    = $services[$serviceInterface]['methods'][$method][Magento_Webapi_Model_Config::ATTR_HTTP_METHOD];
            }
        }
        if (!isset($httpMethod)) {
            throw new Exception("REST HTTP method cannot be identified.");
        }
        return $httpMethod;
    }
}
