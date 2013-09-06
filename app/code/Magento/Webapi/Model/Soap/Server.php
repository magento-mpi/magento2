<?php
/**
 * Magento-specific SOAP server.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
class Mage_Webapi_Model_Soap_Server
=======
class Magento_Webapi_Model_Soap_Server extends \Zend\Soap\Server
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
{
    const SOAP_DEFAULT_ENCODING = 'UTF-8';

    /**#@+
     * Path in config to Webapi settings.
     */
    const CONFIG_PATH_WSDL_CACHE_ENABLED = 'webapi/soap/wsdl_cache_enabled';
    const CONFIG_PATH_SOAP_CHARSET = 'webapi/soap/charset';
    /**#@-*/

    const REQUEST_PARAM_SERVICES = 'services';
    const REQUEST_PARAM_WSDL = 'wsdl';

<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
    /** @var Mage_Core_Model_App */
=======
    /** @var Magento_Core_Model_Store */
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
    protected $_application;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
    /** @var Mage_Webapi_Controller_Soap_Request */
=======
    /** @var Magento_Webapi_Controller_Request_Soap */
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
    protected $_request;

    /** @var Mage_Webapi_Controller_Soap_Handler */
    protected $_soapHandler;

    /**
     * Initialize dependencies.
     *
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
     * @param Mage_Core_Model_App $application
     * @param Mage_Webapi_Controller_Soap_Request $request
=======
     * @param Magento_Core_Model_App $application
     * @param Magento_Webapi_Controller_Request_Soap $request
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
     * @param Magento_DomDocument_Factory $domDocumentFactory
     * @param Mage_Webapi_Controller_Soap_Handler
     * @throws Mage_Webapi_Exception with invalid SOAP extension
     */
    public function __construct(
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
        Mage_Core_Model_App $application,
        Mage_Webapi_Controller_Soap_Request $request,
        Magento_DomDocument_Factory $domDocumentFactory,
        Mage_Webapi_Controller_Soap_Handler $soapHandler
=======
        Magento_Core_Model_App $application,
        Magento_Webapi_Controller_Request_Soap $request,
        Magento_DomDocument_Factory $domDocumentFactory
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
    ) {
        if (!extension_loaded('soap')) {
            throw new Mage_Webapi_Exception('SOAP extension is not loaded.',
                Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }
        $this->_application = $application;
        $this->_request = $request;
        $this->_domDocumentFactory = $domDocumentFactory;
        $this->_soapHandler = $soapHandler;
        $this->_initWsdlCache();
    }

    /**
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
     * Generate exception if request is invalid.
     *
     * @param string $soapRequest
     * @throws Mage_Webapi_Exception with invalid SOAP extension
     * @return Mage_Webapi_Model_Soap_Server
=======
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
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
     */
    protected function _checkRequest($soapRequest)
    {
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
        // TODO: Check why entity loader is required here
        // TODO: Add translation to all Mage_Webapi_Exceptions
        libxml_disable_entity_loader(true);
        $dom = new DOMDocument();
        if (strlen($soapRequest) == 0 || !$dom->loadXML($soapRequest)) {
            throw new Mage_Webapi_Exception('Invalid XML', Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                throw new Mage_Webapi_Exception('Invalid XML: Detected use of illegal DOCTYPE',
                    Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
            }
=======
        try {
            parent::_setRequest($request);
        } catch (Exception $e) {
            $fault = new Magento_Webapi_Model_Soap_Fault(
                $e->getMessage(),
                Magento_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER
            );
            die($fault->toXml($this->_application->isDeveloperMode()));
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
        }
        libxml_disable_entity_loader(false);
        return $this;
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
        $dom->loadXML($this->_request);
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
            self::REQUEST_PARAM_SERVICES => $this->_request->getParam(
                Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES
            )
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
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Soap/Server.php
        return $this->_application->getStore()->getBaseUrl() . $this->_application->getConfig()->getAreaFrontName();
    }

    /**
     * TODO: Fix method description
     * Handle a request
     *
     * Instantiates SoapServer object with options set in object, and
     * dispatches its handle() method.
     * Pulls request using php:://input (for cross-platform compatibility purposes).
     */
    public function handle()
    {
        $soapRequest = file_get_contents('php://input');
        $this->_checkRequest($soapRequest);
        $soap = $this->_createSoapServer();
        $soap->handle($soapRequest);
    }

    /**
     * Instantiate SoapServer object.
     *
     * @return SoapServer
     */
    protected function _createSoapServer()
    {
        $options = array(
            'encoding' => $this->getApiCharset(),
            'soap_version' => SOAP_1_2
        );
        $server  = new SoapServer($this->generateUri(true), $options);
        $server->setObject($this->_soapHandler);
        return $server;
=======
        return $this->_application->getStore()->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_WEB)
            . $this->_application->getConfig()->getAreaFrontName() . '/'
            . Magento_Webapi_Controller_Front::API_TYPE_SOAP;
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Soap/Server.php
    }
}
