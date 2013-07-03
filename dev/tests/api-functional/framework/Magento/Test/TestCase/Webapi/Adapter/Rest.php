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
     * Perform call to the specified service method.
     *
     * @param string $serviceInfo <pre>
     * array(
     *     'rest' => array(
     *         'resourcePath' => $resourcePath,    // e.g. /products/:id
     *         'httpMethod' => $httpMethod // e.g. GET
     *     ),
     *     'soap' => array(
     *         'service' => $soapService,           // soap service name e.g. catalogProduct, customer
     *         'serviceVersion' => $serviceVersion, // with 'V' prefix or without it
     *         'operation' => $operation            // soap operation name e.g. catalogProductCreate
     *     ),
     *     OR
     *     'serviceInterface' => $phpServiceInterfaceName, // e.g. Mage_Catalog_Service_ProductInterfaceV1
     *     'method' => serviceMethodName                   // e.g. create
     * );
     * </pre>
     * @param array $arguments
     * @return array
     * @thows Exception
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

        // delegate the request to vannila cURL REST client
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
                throw new Exception("HttpMethod ${httpMethod} not supported");
        }
        return $returnArray;
    }

    /**
     * Retrieves REST endpoint from $serviceInfo array and returns to the caller
     *
     * @param array $serviceInfo
     * @return string REST endpoind
     * @throws Exception
     */
    protected function _getRestResourcePath($serviceInfo)
    {
        if (isset($serviceInfo['rest']['resourcePath'])) {
            return $serviceInfo['rest']['resourcePath'];
        } else {
            // REST endpoint not specified
            throw new Exception("REST endpoint not specified");
        }
    }

    /**
     * Retrieves REST endpoint from $serviceInfo array and returns to the caller
     *
     * @param array $serviceInfo
     * @return string REST endpoind
     * @throws Exception
     */
    protected function _getRestHttpMethod($serviceInfo)
    {
        if (isset($serviceInfo['rest']['httpMethod'])) {
            return $serviceInfo['rest']['httpMethod'];
        } else {
            // REST endpoint not specified
            throw new Exception("REST httpMethod not specified");
        }
    }
}
