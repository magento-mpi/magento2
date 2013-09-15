<?php
/**
 * Test client for REST API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_TestCase_Webapi_Adapter_Rest implements Magento_Test_TestCase_Webapi_AdapterInterface
{
    /** @var \Magento\Webapi\Model\Config */
    protected $_config;

    /**
     * Initialize dependencies.
     */
    public function __construct()
    {
        $this->_config = Mage::getObjectManager()->get('Magento\Webapi\Model\Config');
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function call($serviceInfo, $arguments = array())
    {
        $resourcePath = $this->_getRestResourcePath($serviceInfo);
        $httpMethod = $this->_getRestHttpMethod($serviceInfo);
        // delegate the request to vanilla cURL REST client
        $curlClient = new Magento_Test_TestCase_Webapi_Adapter_Rest_CurlClient();
        switch ($httpMethod) {
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET:
                $response = $curlClient->get($resourcePath, $arguments);
                break;
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST:
                $response = $curlClient->post($resourcePath, $arguments);
                break;
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT:
                $response = $curlClient->put($resourcePath, $arguments);
                break;
            case \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE:
                $response = $curlClient->delete($resourcePath);
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
                $routePattern = $serviceData[\Magento\Webapi\Model\Config::ATTR_SERVICE_PATH] . $methodData['route'];
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
                    = $services[$serviceInterface]['methods'][$method][\Magento\Webapi\Model\Config::ATTR_HTTP_METHOD];
            }
        }
        if (!isset($httpMethod)) {
            throw new Exception("REST HTTP method cannot be identified.");
        }
        return $httpMethod;
    }
}
