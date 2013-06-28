<?php
/**
 * Test client for REST API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_TesCase_Api_Client_Rest implements Magento_Test_TestCase_Api_ClientInterface
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
            $httpMethod = 'GET';
        }

        // delegate the request to vannila cURL REST client
        $curlClient = new Magento_Test_TesCase_Api_Client_Rest_CurlClient();

        switch ($httpMethod) {
            case 'GET':
                return $curlClient->get($resourcePath, $arguments);
                break;
            case 'POST':
                return $curlClient->post($resourcePath, $arguments);
                break;
            case 'PUT':
                return $curlClient->put($resourcePath, $arguments);
                break;
            case 'DELETE':
                return $curlClient->delete($resourcePath);
                break;
        }
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
        if (isset($serviceInfo['rest']['endpoint'])) {
            return $serviceInfo['soap']['operation'];
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
            return $serviceInfo['soap']['httpMethod'];
        } else {
            // REST endpoint not specified
            throw new Exception("REST httpMethod not specified");
        }
    }
}
