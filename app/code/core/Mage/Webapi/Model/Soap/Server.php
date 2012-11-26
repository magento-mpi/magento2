<?php
/**
 * Magento-specific SOAP server.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_Server
{
    const SOAP_DEFAULT_ENCODING = 'UTF-8';

    /**#@+
     * Path in config to Webapi settings.
     */
    const CONFIG_PATH_WSDL_CACHE_ENABLED = 'webapi/soap/wsdl_cache_enabled';
    const CONFIG_PATH_SOAP_CHARSET = 'webapi/soap/charset';
    /**#@-*/

    const REQUEST_PARAM_RESOURCES = 'resources';
    const REQUEST_PARAM_WSDL = 'wsdl';

    /** @var \Zend\Soap\Server */
    protected $_zendSoapServer;

    /** @var Mage_Webapi_Model_Config */
    protected $_apiConfig;

    /** @var Mage_Core_Model_Store */
    protected $_application;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Controller_Dispatcher_Soap_Handler */
    protected $_soapHandler;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    public function __construct(
        Zend\Soap\Server $zendSoapServer,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Core_Model_App $application,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Dispatcher_Soap_Handler $soapHandler,
        Magento_DomDocument_Factory $domDocumentFactory
    ) {
        $this->_zendSoapServer = $zendSoapServer;
        $this->_apiConfig = $apiConfig;
        $this->_application = $application;
        $this->_request = $request;
        $this->_soapHandler = $soapHandler;
        $this->_domDocumentFactory = $domDocumentFactory;
        $this->_initSoapServer();
    }

    /**
     * Initialize SOAP Server.
     *
     * @throws SoapFault
     */
    protected function _initSoapServer()
    {
        $this->_initWsdlCache();
        $schemaImportTrials = 0;
        do {
            $schemaImportFailed = false;
            try {
                $this->_zendSoapServer
                    ->setWSDL($this->_getWsdlUrl())
                    ->setEncoding($this->getApiCharset())
                    ->setClassmap($this->_apiConfig->getTypeToClassMap());
            } catch (SoapFault $e) {
                $importSchemaMessage = "Can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'";
                if (false !== strpos($e->getMessage(), $importSchemaMessage)) {
                    $schemaImportFailed = true;
                    $schemaImportTrials++;
                    sleep(1);
                } else {
                    throw $e;
                }
            }
        } while ($schemaImportFailed && $schemaImportTrials < 5);
        use_soap_error_handler(false);
        // TODO: Headers are not available at this point.
        // $this->_soapHandler->setRequestHeaders($this->_getRequestHeaders());
        $this->_zendSoapServer->setReturnResponse(true)->setObject($this->_soapHandler);
    }

    /**
     * Get SOAP Header names from request.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _getRequestHeaders()
    {
        $dom = $this->_domDocumentFactory->createDomDocument();
        $dom->loadXML($this->_zendSoapServer->getLastRequest());
        $headers = array();
        /** @var DOMElement $header */
        foreach ($dom->getElementsByTagName('Header')->item(0)->childNodes as $header) {
            list($headerNs, $headerName) = explode(":", $header->nodeName);
            $headers[] = $headerName;
        }

        return $headers;
    }

    /**
     * Handle a request.
     *
     * @param DOMDocument|DOMNode|SimpleXMLElement|stdClass|string $request Optional request
     * @return string|void
     */
    public function handle($request = null)
    {
        return $this->_zendSoapServer->handle($request);
    }

    /**
     * Enable or disable SOAP extension WSDL cache depending on Magento configuration.
     */
    protected function _initWsdlCache()
    {
        $wsdlCacheEnabled = (bool)$this->_application->getStore()->getConfig(self::CONFIG_PATH_WSDL_CACHE_ENABLED);
        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }
    }

    /**
     * Retrieve charset used in SOAP API.
     *
     * @return string
     */
    public function getApiCharset()
    {
        $charset = $this->_application->getStore()->getConfig(self::CONFIG_PATH_SOAP_CHARSET);
        return $charset ? $charset : self::SOAP_DEFAULT_ENCODING;
    }

    /**
     * Get WSDL file URL.
     *
     * @return string
     */
    protected function _getWsdlUrl()
    {
        return $this->generateUri(true);
    }

    /**
     * Get SOAP endpoint URL.
     *
     * @param bool $isWsdl
     * @return string
     */
    public function generateUri($isWsdl = false)
    {
        $params = array(
            self::REQUEST_PARAM_RESOURCES => $this->_request->getRequestedResources()
        );
        if ($isWsdl) {
            $params[self::REQUEST_PARAM_WSDL] = true;
        }
        $query = http_build_query($params, '', '&');
        return $this->getEndpointUri() . '?' . $query;
    }

    /**
     * Generate URI of SOAP endpoint.
     *
     * @return string
     */
    public function getEndpointUri()
    {
        // @TODO: Implement proper endpoint URL retrieval mechanism in APIA-718 story
        return $this->_application->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
            . Mage_Webapi_Controller_Router_Route_Webapi::API_AREA_NAME . '/'
            . Mage_Webapi_Controller_Front::API_TYPE_SOAP;
    }
}
