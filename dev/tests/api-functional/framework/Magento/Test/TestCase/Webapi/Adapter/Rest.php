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
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function call($serviceInfo, $arguments = array())
    {
        // get endpoint & httpMethod are present
        try {
            $resourcePath = $this->_getRestResourcePath($serviceInfo);
        } catch (Exception $e) {
            // REST endpoint not defined, skip making REST call
            // TODO: Log Message
            return;
        }

        // use HTTP GET as default
        try {
            $httpMethod = $this->_getRestHttpMethod($serviceInfo);
        } catch (Exception $e) {
            // default httpMethod to GET
            $httpMethod = HTTP_REQUEST_METHOD_GET;
        }

        // delegate the request to vanilla cURL REST client
        $curlClient = new Magento_Test_TestCase_Webapi_Adapter_Rest_CurlClient();

        $returnArray = array();
        switch ($httpMethod) {
            case HTTP_REQUEST_METHOD_GET:
                $returnArray = $curlClient->get($resourcePath, $arguments);
                break;
            case HTTP_REQUEST_METHOD_POST:
                $returnArray = $curlClient->post($resourcePath, $arguments);
                break;
            case HTTP_REQUEST_METHOD_PUT:
                $returnArray = $curlClient->put($resourcePath, $arguments);
                break;
            case HTTP_REQUEST_METHOD_DELETE:
                $returnArray = $curlClient->delete($resourcePath);
                break;
            default:
                throw new Exception("HttpMethod {$httpMethod} not supported");
        }
        return $returnArray;
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
