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
    /**#@+
     * Supported HTTP methods
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    /**#@-*/

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
            case self::HTTP_METHOD_GET:
                $response = $curlClient->get($resourcePath, $arguments);
                break;
            case self::HTTP_METHOD_POST:
                $response = $curlClient->post($resourcePath, $arguments);
                break;
            case self::HTTP_METHOD_PUT:
                $response = $curlClient->put($resourcePath, $arguments);
                break;
            case self::HTTP_METHOD_DELETE:
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
            return $serviceInfo['rest']['resourcePath'];
        } else {
            throw new Exception("REST endpoint not specified");
        }
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
            return $serviceInfo['rest']['httpMethod'];
        } else {
            throw new Exception("REST httpMethod not specified");
        }
    }
}
