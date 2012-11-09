<?php
/**
 * Front controller for SOAP API. At the same time it is a handler for SOAP server
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Front_Soap extends Mage_Webapi_Controller_FrontAbstract
{
    const BASE_ACTION_CONTROLLER = 'Mage_Webapi_Controller_ActionAbstract';

    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';

    const FAULT_REASON_INTERNAL = 'Internal Error.';

    const WEBSERVICE_CACHE_NAME = 'config_webservice';
    const WEBSERVICE_CACHE_TAG = 'WEBSERVICE';
    const WSDL_CACHE_ID = 'WSDL';

    const REQUEST_PARAM_RESOURCES = 'resources';
    const REQUEST_PARAM_WSDL = 'wsdl';

    /** @var Zend\Soap\Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Mage_Core_Model_Store */
    protected $_application;

    /** @var Mage_Core_Model_Cache */
    protected $_cache;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /**
     * WS-Security UsernameToken object from request
     *
     * @var stdClass
     */
    protected $_usernameTokenRequest;

    function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Core_Model_Config $applicationConfig,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Controller_ActionFactory $actionControllerFactory,
        Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Zend\Soap\Server $soapServer,
        Mage_Core_Model_App $application,
        Mage_Core_Model_Cache $cache,
        Magento_DomDocument_Factory $domDocumentFactory
    ) {
        parent::__construct($helper, $applicationConfig, $apiConfig, $response, $actionControllerFactory);
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_cache = $cache;
        $this->_application = $application;
        $this->_domDocumentFactory = $domDocumentFactory;
    }

    /**
     * Handler for all SOAP operations
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass
     */
    // TODO: Think about situations when custom error handler is required for this method (that can throw SOAP faults)
    public function __call($operation, $arguments)
    {
        if (in_array($operation, $this->_getRequestedHeaders())) {
            $this->_processSoapHeader($operation, $arguments);
        } else {
            $resourceVersion = $this->_getOperationVersion($operation);
            $resourceName = $this->getApiConfig()->getResourceNameByOperation($operation, $resourceVersion);
            if (!$resourceName) {
                $this->_soapFault(sprintf('Method "%s" not found.', $operation), self::FAULT_CODE_SENDER);
            }
            $controllerClass = $this->getApiConfig()->getControllerClassByOperationName($operation);
            $controllerInstance = $this->_getActionControllerInstance($controllerClass);
            $method = $this->getApiConfig()->getMethodNameByOperation($operation, $resourceVersion);
            try {
                // TODO: Uncomment after DI refactoring
//                $this->_checkResourceAcl($resourceName, $method);

                $arguments = reset($arguments);
                $arguments = get_object_vars($arguments);
                $versionAfterFallback = $this->_identifyVersionSuffix($operation, $resourceVersion,
                    $controllerInstance);
                $this->_checkDeprecationPolicy($resourceName, $method, $versionAfterFallback);
                $action = $method . $versionAfterFallback;
                $arguments = $this->getHelper()->prepareMethodParams($controllerClass, $action, $arguments,
                    $this->getApiConfig());
//            $inputData = $this->_presentation->fetchRequestData($operation, $controllerInstance, $action);
                $outputData = call_user_func_array(array($controllerInstance, $action), $arguments);
                // TODO: Implement response preparation according to current presentation
//            $this->_presentation->prepareResponse($operation, $outputData);
                return (object)array('result' => $outputData);
            } catch (Mage_Webapi_Exception $e) {
                $this->_soapFault($e->getMessage(), $e->getOriginator(), $e);
            } catch (Exception $e) {
                if (!Mage::getIsDeveloperMode()) {
                    Mage::logException($e);
                    $this->_soapFault($this->_helper->__("Internal Error. Details are available in Magento log file."));
                } else {
                    $this->_soapFault($this->_helper->__("Internal Error."), self::FAULT_CODE_RECEIVER, $e);
                }
            }
        }
    }

    /**
     * Handle SOAP headers.
     *
     * @param string $header
     * @param array $arguments
     */
    protected function _processSoapHeader($header, $arguments)
    {
        switch ($header) {
            case 'Security':
                foreach ($arguments as $argument) {
                    if (is_object($argument) && isset($argument->UsernameToken)) {
                        $this->_usernameTokenRequest = $argument->UsernameToken;
                    }
                }
                break;
        }
    }

    /**
     * Get SOAP Header names from request.
     *
     * @return array
     */
    protected function _getRequestedHeaders()
    {
        $dom = $this->_domDocumentFactory->createDomDocument();
        $dom->loadXML($this->_getSoapServer()->getLastRequest());
        $headers = array();
        /** @var DOMElement $header */
        foreach ($dom->getElementsByTagName('Header')->item(0)->childNodes as $header) {
            list($headerNs, $headerName) = explode(":", $header->nodeName);
            $headers[] = $headerName;
        }

        return $headers;
    }

    /**
     * Authenticate user
     */
    protected function _authenticate()
    {
        if (is_null($this->_usernameTokenRequest)) {
            $this->_soapFault($this->_helper->__('No WS-Security UsernameToken found in SOAP-request.'),
                self::FAULT_CODE_SENDER);
        }

        try {
            /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
            $usernameToken = Mage::getModel('Mage_Webapi_Model_Soap_Security_UsernameToken', array(
                'username' => $this->_usernameTokenRequest->Username,
                'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_DIGEST,
                'password' => $this->_usernameTokenRequest->Password,
                'nonce' => $this->_usernameTokenRequest->Nonce,
                'created' => $this->_usernameTokenRequest->Created
            ));
            Mage::getSingleton('Mage_Webapi_Model_Authorization_RoleLocator')
                ->setRoleId($usernameToken->authenticate()->getRoleId());
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException $e) {
            $this->_soapFault($this->_helper->__('WS-Security UsernameToken Nonce is already used.'),
                self::FAULT_CODE_SENDER);
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException $e) {
            $this->_soapFault($this->_helper->__('WS-Security UsernameToken Created timestamp is refused.'),
                self::FAULT_CODE_SENDER);
        } catch(Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException $e) {
            $this->_soapFault($this->_helper->__('Invalid Username or Password.'), self::FAULT_CODE_SENDER);
        } catch (Exception $e) {
            $this->_soapFault($this->_helper->__('Error during authenticating SOAP-request.'), self::FAULT_CODE_SENDER,
                $e);
        }
    }

    /**
     * Get SOAP request.
     *
     * @return Mage_Webapi_Controller_Request_Soap
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Implementation of abstract method.
     *
     * @return Mage_Webapi_Controller_Front_Soap|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        $this->_initSoapServer();
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Front_Soap
     */
    public function dispatch()
    {
        try {
            if ($this->getRequest()->getParam(self::REQUEST_PARAM_WSDL) !== null) {
                $this->_setResponseContentType('text/xml');
                $responseBody = $this->_getWsdlContent();
            } else {
                $this->_setResponseContentType('application/soap+xml');
                $responseBody = $this->_getSoapServer()->handle();
            }
            $this->_setResponseBody($responseBody);
        } catch (Mage_Webapi_Exception $e) {
            self::_processBadRequest($e->getMessage());
        } catch (Exception $e) {
            self::_processBadRequest($this->_helper->__('Internal error.'));
        }

        $this->getResponse()->sendResponse();
        return $this;
    }

    /**
     * Process request as HTTP 400 and set error message.
     *
     * @param string $message
     */
    protected function _processBadRequest($message)
    {
        $this->_setResponseContentType('text/xml');
        $this->getResponse()->setHttpResponseCode(400);

        $apiUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/soap';

        $details = array();
        $resourceConfig = $this->getApiConfig();
        if (!is_null($resourceConfig)) {
            foreach ($resourceConfig->getAllResourcesVersions() as $resourceName => $versions) {
                foreach ($versions as $version) {
                    $details['availableResources'][$resourceName][$version] = sprintf('%s?wsdl&resources[%s]=%s',
                        $apiUrl, $resourceName, $version);
                }
            }
        }
        $this->_setResponseBody($this->_getSoapFaultMessage($message, self::FAULT_CODE_SENDER, 'en', $details));
    }

    /**
     * Generate WSDL content based on resource config.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _getWsdlContent()
    {
        $requestedResources = $this->getRequest()->getRequestedResources();
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedResources));
        if ($this->_cache->canUse(self::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = $this->_cache->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        $resources = array();
        try {
            foreach ($requestedResources as $resourceName => $resourceVersion) {
                $resources[$resourceName] = $this->getApiConfig()
                    ->getResourceDataMerged($resourceName, $resourceVersion);
            }
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception($e->getMessage(), Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $wsdlContent = $this->_autoDiscover->generate($resources, $this->_getEndpointUrl());

        if ($this->_cache->canUse(self::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save($wsdlContent, $cacheId, array(self::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
    }

    /**
     * Retrieve SOAP Server.
     *
     * @return Zend\Soap\Server
     * @throws SoapFault
     */
    protected function _getSoapServer()
    {
        return $this->_soapServer;
    }

    /**
     * Initialize Soap Server.
     *
     * @throws SoapFault
     */
    protected function _initSoapServer()
    {
        $this->_initWsdlCache();
        $soapSchemaImportTriesCount = 0;
        do {
            $soapSchemaImportFailed = false;
            try {
                $this->_soapServer
                    ->setWSDL($this->_getWsdlUrl())
                    ->setEncoding($this->_getApiCharset())
                    ->setClassmap($this->getApiConfig()->getTypeToClassMap());
            } catch (SoapFault $e) {
                if (false !== strpos($e->getMessage(),
                    "Can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'")
                ) {
                    $soapSchemaImportFailed = true;
                    $soapSchemaImportTriesCount++;
                    sleep(1);
                } else {
                    throw $e;
                }
            }
        } while ($soapSchemaImportFailed && $soapSchemaImportTriesCount < 5);
        use_soap_error_handler(false);
        // Front controller plays the role of SOAP handler
        $this->_soapServer->setReturnResponse(true)->setObject($this);
    }

    /**
     * Set content type to response object
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Front_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->getResponse()->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object
     *
     * @param string $responseBody
     * @return Mage_Webapi_Controller_Front_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->getResponse()->setBody(preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
    }

    /**
     * Retrieve charset used in API
     *
     * @return string
     */
    protected function _getApiCharset()
    {
        // TODO: What do we need this charset for?
        return $this->_application->getStore()->getConfig('api/config/charset');
    }

    /**
     * Enable or disable SOAP extension WSDL cache depending on Magento configuration
     */
    protected function _initWsdlCache()
    {
        $wsdlCacheEnabled = (bool)$this->_application->getStore()->getConfig('api/config/wsdl_cache_enabled');
        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }
    }

    /**
     * Get WSDL file URL
     *
     * @return string
     */
    protected function _getWsdlUrl()
    {
        return $this->_getEndpointUrl(true);
    }

    /**
     * Get SOAP endpoint URL
     *
     * @param bool $isWsdl
     * @return string
     */
    protected function _getEndpointUrl($isWsdl = false)
    {
        $params = array(
            self::REQUEST_PARAM_RESOURCES => $this->getRequest()->getRequestedResources()
        );
        if ($isWsdl) {
            $params[self::REQUEST_PARAM_WSDL] = true;
        }
        $query = http_build_query($params, '', '&');
        // @TODO: Implement proper endpoint URL retrieval mechanism in APIA-718 story
        return $this->_application->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/soap?' . $query;
    }

    /**
     * Generate SOAP fault
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param Exception $e Exception can be used to add information to Detail node of SOAP message
     * @throws SoapFault
     */
    protected function _soapFault($reason = self::FAULT_REASON_INTERNAL, $code = self::FAULT_CODE_RECEIVER,
        Exception $e = null
    ) {
        header('Content-type: application/soap+xml; charset=UTF-8');
        if ($this->_isSoapExtensionLoaded()) {
            $details = null;
            if (!is_null($e)) {
                $details = array('ExceptionCode' => $e->getCode());
                // add detailed message only if it differs from fault reason
                if ($e->getMessage() != $reason) {
                    $details['ExceptionMessage'] = $e->getMessage();
                }
                if (Mage::getIsDeveloperMode()) {
                    $details['ExceptionTrace'] = "<![CDATA[{$e->getTraceAsString()}]]>";
                }
            }
            // TODO: Implement Current language definition
            $language = 'en';
            die($this->_getSoapFaultMessage($reason, $code, $language, $details));
        } else {
            die($this->_getSoapFaultMessage(self::FAULT_CODE_RECEIVER, 'SOAP extension is not loaded.'));
        }
    }

    /**
     * Generate SOAP fault message in xml format
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param string $language Reason message language
     * @param string|array|null $details Detailed reason message(s)
     * @return string
     */
    protected function _getSoapFaultMessage($reason = self::FAULT_REASON_INTERNAL, $code = self::FAULT_CODE_RECEIVER,
        $language = 'en', $details = null
    ) {
        if (is_string($details)) {
            $detailsXml = "<env:Detail>" . htmlspecialchars($details) . "</env:Detail>";
        } elseif (is_array($details)) {
            $detailsXml = "<env:Detail>" . $this->_convertDetailsToXml($details) . "</env:Detail>";
        } else {
            $detailsXml = '';
        }
        $reason = htmlentities($reason);
        $message = <<<FAULT_MESSAGE
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
   <env:Body>
      <env:Fault>
         <env:Code>
            <env:Value>$code</env:Value>
         </env:Code>
         <env:Reason>
            <env:Text xml:lang="$language">$reason</env:Text>
         </env:Reason>
         $detailsXml
      </env:Fault>
   </env:Body>
</env:Envelope>
FAULT_MESSAGE;
        return $message;
    }

    /**
     * Recursively convert details array into xml structure.
     *
     * @param array $details
     * @return string
     */
    protected function _convertDetailsToXml($details)
    {
        $detailsXml = '';
        foreach ($details as $detailNode => $detailValue) {
            $detailNode = htmlspecialchars($detailNode);
            if (is_numeric($detailNode)) {
                continue;
            }
            if (is_string($detailValue)) {
                $detailsXml .= "<$detailNode>" . htmlspecialchars($detailValue) . "</$detailNode>";
            } elseif (is_array($detailValue)) {
                $detailsXml .= "<$detailNode>" . $this->_convertDetailsToXml($detailValue) . "</$detailNode>";
            }
        }
        return $detailsXml;
    }

    /**
     * Check whether SOAP extension is loaded or not
     *
     * @return boolean
     */
    protected function _isSoapExtensionLoaded()
    {
        return class_exists('SoapServer', false);
    }

    /**
     * Identify version of requested operation.
     *
     * This method required when there are two or more resource versions specified in request:
     * http://magento.host/api/soap?wsdl&resources[resource_a]=v1&resources[resource_b]=v2 <br/>
     * In this case it is not obvious what version of requested operation should be used.
     *
     * @param string $operationName
     * @return int
     * @throws Mage_Webapi_Exception
     */
    protected function _getOperationVersion($operationName)
    {
        $requestedResources = $this->getRequest()->getRequestedResources();
        $resourceName = $this->getApiConfig()->getResourceNameByOperation($operationName);
        if (!isset($requestedResources[$resourceName])) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__('The version of "%s" operation cannot be identified.', $operationName),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        $version = (int)str_replace('V', '', ucfirst($requestedResources[$resourceName]));
        $this->_validateVersionNumber($version, $resourceName);
        return $version;
    }
}
