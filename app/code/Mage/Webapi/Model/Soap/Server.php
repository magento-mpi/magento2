<?php
/**
 * Magento-specific SOAP server.
 * TODO: Remove dependency on Zend SOAP Server and methods overrides.
 * TODO: Remove dependence on application config, probably move it to dispatcher.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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

    const REQUEST_PARAM_SERVICES = 'services';
    const REQUEST_PARAM_WSDL = 'wsdl';

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_request;

    /**
     * URI or path to WSDL
     * @var string
     */
    protected $wsdl;

    /**
     * Encoding
     * @var string
     */
    protected $encoding;

    /**
     * SOAP version to use; SOAP_1_2 by default, to allow processing of headers
     * @var int
     */
    protected $soapVersion = SOAP_1_2;

    /**
     * Arguments to pass to {@link $class} constructor
     * @var array
     */
    protected $classArgs = array();

    /**
     * Request XML
     * @var string
     */
    protected $request;

    /**
     * Response XML
     * @var string
     */
    protected $response;

    /**
     * Flag: whether or not {@link handle()} should return a response instead
     * of automatically emitting it.
     * @var boolean
     */
    protected $returnResponse = false;

    /**
     * Registered fault exceptions
     * @var array
     */
    protected $faultExceptions = array();

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_App $application
     * @param Mage_Webapi_Controller_Soap_Request $request
     * @param Magento_DomDocument_Factory $domDocumentFactory
     * @throws Mage_Webapi_Exception with invalid SOAP extension
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Webapi_Controller_Soap_Request $request,
        Magento_DomDocument_Factory $domDocumentFactory
    ) {
        if (!extension_loaded('soap')) {
            throw new Mage_Webapi_Exception('SOAP extension is not loaded.',
                Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }

        $this->_application = $application;
        $this->_request = $request;
        $this->_domDocumentFactory = $domDocumentFactory;
    }

    /**
     * Process Webapi SOAP fault.
     *
     * @param Mage_Webapi_Model_Soap_Fault|Exception|string $fault
     * @param string $code
     * @return SoapFault|string
     */
    public function fault($fault = null, $code = null)
    {
        if ($fault instanceof Mage_Webapi_Model_Soap_Fault) {
            return $fault->toXml($this->_application->isDeveloperMode());
        } else {
            if ($fault instanceof \Exception) {
                $class = get_class($fault);
                if (in_array($class, $this->faultExceptions)) {
                    $message = $fault->getMessage();
                    $eCode   = $fault->getCode();
                    $code    = empty($eCode) ? $code : $eCode;
                } else {
                    $message = 'Unknown error';
                }
            } elseif (is_string($fault)) {
                $message = $fault;
            } else {
                $message = 'Unknown error';
            }

            $allowedFaultModes = array(
                'VersionMismatch', 'MustUnderstand', 'DataEncodingUnknown',
                'Sender', 'Receiver', 'Server'
            );
            if (!in_array($code, $allowedFaultModes)) {
                $code = "Receiver";
            }

            return new \SoapFault($code, $message);
        }
    }

    /**
     * Catch exceptions if request is invalid and output fault message.
     *
     * @param DOMDocument|DOMNode|SimpleXMLElement|stdClass|string $request
     * @throws Mage_Webapi_Exception with invalid SOAP extension
     * @return Mage_Webapi_Model_Soap_Server
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _setRequest($request)
    {
        try {
            if ($request instanceof DOMDocument) {
                $xml = $request->saveXML();
            } elseif ($request instanceof DOMNode) {
                $xml = $request->ownerDocument->saveXML();
            } elseif ($request instanceof SimpleXMLElement) {
                $xml = $request->asXML();
            } elseif (is_object($request) || is_string($request)) {
                if (is_object($request)) {
                    $xml = $request->__toString();
                } else {
                    $xml = $request;
                }
                libxml_disable_entity_loader(true);
                $dom = new DOMDocument();
                if (strlen($xml) == 0 || !$dom->loadXML($xml)) {
                    throw new Mage_Webapi_Exception('Invalid XML', Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
                }
                foreach ($dom->childNodes as $child) {
                    if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                        throw new Mage_Webapi_Exception('Invalid XML: Detected use of illegal DOCTYPE',
                            Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
                    }
                }
                libxml_disable_entity_loader(false);
            }
            $this->request = $xml;
            return $this;
        } catch (Exception $e) {
            $fault = new Mage_Webapi_Model_Soap_Fault(
                $e->getMessage(),
                Mage_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER,
                $this->_application->getLocale()->getLocale()->getLanguage()
            );
            die($fault->toXml($this->_application->isDeveloperMode()));
        }
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
        return $charset ? $charset : Mage_Webapi_Model_Soap_Server::SOAP_DEFAULT_ENCODING;
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
        return $this->_application->getStore()->getBaseUrl()
        . Mage_Webapi_Controller_Soap::REQUEST_TYPE;
    }

    /**
     * Set wsdl
     *
     * @param string $wsdl  URI or path to a WSDL
     * @return Mage_Webapi_Model_Soap_Server
     */
    public function setWSDL($wsdl)
    {
        $this->wsdl = $wsdl;
        return $this;
    }

    /**
     * Retrieve wsdl
     *
     * @return string
     */
    public function getWSDL()
    {
        return $this->wsdl;
    }

    /**
     * Set encoding
     *
     * @param  string $encoding
     * @return Mage_Webapi_Model_Soap_Server
     * @throws Mage_Webapi_Exception with invalid encoding argument
     */
    public function setEncoding($encoding)
    {
        if (!is_string($encoding)) {
            throw new Mage_Webapi_Exception('Invalid encoding specified',
                Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }

        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Set SOAP version
     *
     * @param  int $version One of the SOAP_1_1 or SOAP_1_2 constants
     * @return Mage_Webapi_Model_Soap_Server
     * @throws Mage_Webapi_Exception with invalid soap version argument
     */
    public function setSoapVersion($version)
    {
        if (!in_array($version, array(SOAP_1_1, SOAP_1_2))) {
            throw new Mage_Webapi_Exception('Invalid soap version specified',
                Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }

        $this->soapVersion = $version;
        return $this;
    }

    /**
     * Set return response flag
     *
     * If true, {@link handle()} will return the response instead of
     * automatically sending it back to the requesting client.
     *
     * The response is always available via {@link getResponse()}.
     *
     * @param boolean $flag
     * @return Mage_Webapi_Model_Soap_Server
     */
    public function setReturnResponse($flag = true)
    {
        $this->returnResponse = ($flag) ? true : false;
        return $this;
    }

    /**
     * Attach an object to a server
     *
     * Accepts an instanciated object to use when handling requests.
     *
     * @param object $object
     * @throws Mage_Webapi_Exception
     * @return Mage_Webapi_Model_Soap_Server
     */
    public function setObject($object)
    {
        if (!is_object($object)) {
            throw new Mage_Webapi_Exception('Invalid object argument ('.gettype($object).')',
                Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }

        if (isset($this->object)) {
            throw new Mage_Webapi_Exception('An object has already been registered with this soap server instance',
                Mage_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }

        $this->object = $object;

        return $this;
    }

    /**
     * Handle a request
     *
     * Instantiates SoapServer object with options set in object, and
     * dispatches its handle() method.
     *
     * $request may be any of:
     * - DOMDocument; if so, then cast to XML
     * - DOMNode; if so, then grab owner document and cast to XML
     * - SimpleXMLElement; if so, then cast to XML
     * - stdClass; if so, calls __toString() and verifies XML
     * - string; if so, verifies XML
     *
     * If no request is passed, pulls request using php:://input (for
     * cross-platform compatibility purposes).
     *
     * @param DOMDocument|DOMNode|SimpleXMLElement|stdClass|string $request Optional request
     * @return void|string
     */
    public function handle($request = null)
    {
        if (null === $request) {
            $request = file_get_contents('php://input');
        }

        // Set Server error handler
        $displayErrorsOriginalState = $this->_initializeSoapErrorContext();

        $setRequestException = null;
        try {
            $this->_setRequest($request);
        } catch (\Exception $e) {
            $setRequestException = $e;
        }

        $soap = $this->_getSoap();

        $fault = false;
        ob_start();
        if ($setRequestException instanceof \Exception) {
            // Create SOAP fault message if we've caught a request exception
            $fault = $this->fault($setRequestException->getMessage(), 'Sender');
        } else {
            try {
                $soap->handle($this->request);
            } catch (\Exception $e) {
                $fault = $this->fault($e);
            }
        }
        $this->response = ob_get_clean();

        // Restore original error handler
        restore_error_handler();
        ini_set('display_errors', $displayErrorsOriginalState);

        // Send a fault, if we have one
        if ($fault) {
            $this->response = $fault;
        }

        if (!$this->returnResponse) {
            echo $this->response;
            return;
        }

        return $this->response;
    }

    /**
     * Method initializes the error context that the SOAPServer environment will run in.
     *
     * @return boolean display_errors original value
     */
    protected function _initializeSoapErrorContext()
    {
        $displayErrorsOriginalState = ini_get('display_errors');
        ini_set('display_errors', false);
        set_error_handler(array($this, 'handlePhpErrors'), E_USER_ERROR);
        return $displayErrorsOriginalState;
    }

    /**
     * Get SoapServer object
     *
     * Uses {@link $wsdl} and return value of {@link getOptions()} to instantiate
     * SoapServer object, and then registers any functions or class with it, as
     * well as persistence.
     *
     * @return \SoapServer
     */
    protected function _getSoap()
    {
        $options = $this->getOptions();
        $server  = new \SoapServer($this->wsdl, $options);

        if (!empty($this->functions)) {
            $server->addFunction($this->functions);
        }

        if (!empty($this->class)) {
            $args = $this->classArgs;
            array_unshift($args, $this->class);
            call_user_func_array(array($server, 'setClass'), $args);
        }

        if (!empty($this->object)) {
            $server->setObject($this->object);
        }

        return $server;
    }

    /**
     * Return array of options suitable for using with SoapServer constructor
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();
        if (null !== $this->encoding) {
            $options['encoding'] = $this->encoding;
        }

        if (null !== $this->soapVersion) {
            $options['soap_version'] = $this->soapVersion;
        }

        return $options;
    }

}
