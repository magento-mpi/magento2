<?php
/**
 * Magento-specific SOAP server.
 * TODO: Remove dependency on Zend SOAP Server and methods overrides. Create Magento_Soap_Server instead.
 * TODO: Remove dependence on application config, probably move it to dispatcher.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Server extends \Zend\Soap\Server
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

    /** @var Magento_Core_Model_Store */
    protected $_application;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Magento_Webapi_Controller_Request_Soap */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Model_App $application
     * @param Magento_Webapi_Controller_Request_Soap $request
     * @param Magento_DomDocument_Factory $domDocumentFactory
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Webapi_Controller_Request_Soap $request,
        Magento_DomDocument_Factory $domDocumentFactory
    ) {
        parent::__construct();

        $this->_application = $application;
        $this->_request = $request;
        $this->_domDocumentFactory = $domDocumentFactory;
    }

    /**
     * Process Webapi SOAP fault.
     *
     * @param Magento_Webapi_Model_Soap_Fault|Exception|string $fault
     * @param string $code
     * @return SoapFault|string
     */
    public function fault($fault = null, $code = null)
    {
        if ($fault instanceof Magento_Webapi_Model_Soap_Fault) {
            return $fault->toXml($this->_application->isDeveloperMode());
        } else {
            return parent::fault($fault, $code);
        }
    }

    /**
     * Catch exceptions if request is invalid and output fault message.
     *
     * @param DOMDocument|DOMNode|SimpleXMLElement|stdClass|string $request
     * @return Magento_Webapi_Model_Soap_Server
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _setRequest($request)
    {
        try {
            parent::_setRequest($request);
        } catch (Exception $e) {
            $fault = new Magento_Webapi_Model_Soap_Fault(
                $e->getMessage(),
                Magento_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER
            );
            die($fault->toXml($this->_application->isDeveloperMode()));
        }
        return $this;
    }

    /**
     * Suppress PHP error output because it has already been displayed by SoapServer extension.
     * TODO: remove this method when removing dependence on Zend/Soap/Server
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function handlePhpErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
    {
        die();
    }

    /**
     * Get SOAP Header names from request.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getRequestHeaders()
    {
        $dom = $this->_domDocumentFactory->createDomDocument();
        $dom->loadXML($this->getLastRequest());
        $headers = array();
        /** @var DOMElement $header */
        foreach ($dom->getElementsByTagName('Header')->item(0)->childNodes as $header) {
            list($headerNs, $headerName) = explode(":", $header->nodeName);
            $headers[] = $headerName;
        }

        return $headers;
    }

    /**
     * Enable or disable SOAP extension WSDL cache depending on Magento configuration.
     */
    public function initWsdlCache()
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
        return $charset ? $charset : Magento_Webapi_Model_Soap_Server::SOAP_DEFAULT_ENCODING;
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
        return $this->_application->getStore()->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_WEB)
            . $this->_application->getConfig()->getAreaFrontName() . '/'
            . Magento_Webapi_Controller_Front::API_TYPE_SOAP;
    }
}
