<?php
/**
 * Handler for SOAP API calls.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Handler_Soap extends Mage_Webapi_Controller_HandlerAbstract
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

    const SOAP_DEFAULT_ENCODING = 'UTF-8';

    /**#@+
     * Path in config to Webapi settings.
     */
    const CONFIG_PATH_WSDL_CACHE_ENABLED = 'webapi/soap/wsdl_cache_enabled';
    const CONFIG_PATH_SOAP_CHARSET = 'webapi/soap/charset';
    /**#@-*/

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
     * Username token factory.
     *
     * @var Mage_Webapi_Model_Soap_Security_UsernameToken_Factory
     */
    protected $_tokenFactory;

    /**
     * WS-Security UsernameToken object from request.
     *
     * @var stdClass
     */
    protected $_usernameToken;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Config $applicationConfig
     * @param Mage_Webapi_Model_Config $apiConfig
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Controller_Action_Factory $controllerFactory
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover
     * @param Zend\Soap\Server $soapServer
     * @param Mage_Core_Model_App $application
     * @param Mage_Core_Model_Cache $cache
     * @param Magento_DomDocument_Factory $domDocumentFactory
     * @param Mage_Webapi_Model_Soap_Security_UsernameToken_Factory $usernameTokenFactory
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Webapi_Model_Authorization_RoleLocator $roleLocator
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Config $applicationConfig,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Controller_Request_Factory $requestFactory,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Controller_Action_Factory $controllerFactory,
        Mage_Core_Model_Logger $logger,
        Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Zend\Soap\Server $soapServer,
        Mage_Core_Model_App $application,
        Mage_Core_Model_Cache $cache,
        Magento_DomDocument_Factory $domDocumentFactory,
        Mage_Webapi_Model_Soap_Security_UsernameToken_Factory $usernameTokenFactory,
        Magento_ObjectManager $objectManager,
        Mage_Webapi_Model_Authorization_RoleLocator $roleLocator
    ) {
        parent::__construct(
            $helperFactory,
            $applicationConfig,
            $apiConfig,
            $requestFactory,
            $response,
            $controllerFactory,
            $logger,
            $objectManager,
            $roleLocator
        );
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_cache = $cache;
        $this->_application = $application;
        $this->_domDocumentFactory = $domDocumentFactory;
        $this->_tokenFactory = $usernameTokenFactory;
    }

    /**
     * Handler for all SOAP operations.
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass
     */
    public function __call($operation, $arguments)
    {
        if (in_array($operation, $this->_getRequestedHeaders())) {
            $this->_processSoapHeader($operation, $arguments);
        } else {
            $this->_authenticate();
            $resourceVersion = $this->_getOperationVersion($operation);
            $resourceName = $this->getApiConfig()->getResourceNameByOperation($operation, $resourceVersion);
            if (!$resourceName) {
                $this->_soapFault(sprintf('Method "%s" is not found.', $operation), self::FAULT_CODE_SENDER);
            }
            $controllerClass = $this->getApiConfig()->getControllerClassByOperationName($operation);
            $controllerInstance = $this->_getActionControllerInstance($controllerClass);
            $method = $this->getApiConfig()->getMethodNameByOperation($operation, $resourceVersion);
            try {
                $this->_checkResourceAcl($resourceName, $method);

                $arguments = reset($arguments);
                $arguments = get_object_vars($arguments);
                $versionAfterFallback = $this->_identifyVersionSuffix(
                    $operation,
                    $resourceVersion,
                    $controllerInstance
                );
                $this->_checkDeprecationPolicy($resourceName, $method, $versionAfterFallback);
                $action = $method . $versionAfterFallback;
                $arguments = $this->getHelper()->prepareMethodParams(
                    $controllerClass,
                    $action,
                    $arguments,
                    $this->getApiConfig()
                );
                $outputData = call_user_func_array(array($controllerInstance, $action), $arguments);
                return (object)array('result' => $outputData);
            } catch (Mage_Webapi_Exception $e) {
                $this->_soapFault($e->getMessage(), $e->getOriginator(), $e);
            } catch (Exception $e) {
                if (!Mage::getIsDeveloperMode()) {
                    $this->_logger->logException($e);
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
                    // @codingStandardsIgnoreStart
                    if (is_object($argument) && isset($argument->UsernameToken)) {
                        $this->_usernameToken = $argument->UsernameToken;
                    }
                    // @codingStandardsIgnoreEnd
                }
                break;
        }
    }

    /**
     * Get SOAP Header names from request.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
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
     * Authenticate user.
     */
    protected function _authenticate()
    {
        if (is_null($this->_usernameToken)) {
            $this->_soapFault(
                $this->_helper->__('WS-Security UsernameToken is not found in SOAP-request.'),
                self::FAULT_CODE_SENDER
            );
        }

        try {
            $token = $this->_tokenFactory->createFromArray();
            $request = $this->_usernameToken;
            // @codingStandardsIgnoreStart
            $user = $token->authenticate($request->Username, $request->Password, $request->Created, $request->Nonce);
            // @codingStandardsIgnoreEnd
            $this->_roleLocator->setRoleId($user->getRoleId());
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException $e) {
            $this->_soapFault(
                $this->_helper->__('WS-Security UsernameToken Nonce is already used.'),
                self::FAULT_CODE_SENDER
            );
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException $e) {
            $this->_soapFault(
                $this->_helper->__('WS-Security UsernameToken Created timestamp is refused.'),
                self::FAULT_CODE_SENDER
            );
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException $e) {
            $this->_soapFault($this->_helper->__('Invalid Username or Password.'), self::FAULT_CODE_SENDER);
        } catch (Exception $e) {
            $this->_soapFault(
                $this->_helper->__('Error during authenticating SOAP-request.'),
                self::FAULT_CODE_SENDER,
                $e
            );
        }
    }

    /**
     * Implementation of abstract method.
     *
     * @return Mage_Webapi_Controller_Handler_Soap|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        parent::init();
        $this->_initSoapServer();
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Handler_Soap
     */
    public function handle()
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
        $details = array();
        $resourceConfig = $this->getApiConfig();
        if (!is_null($resourceConfig)) {
            foreach ($resourceConfig->getAllResourcesVersions() as $resourceName => $versions) {
                foreach ($versions as $version) {
                    $details['availableResources'][$resourceName][$version] = sprintf(
                        '%s?wsdl&resources[%s]=%s',
                        $this->_getEndpointUri(),
                        $resourceName,
                        $version
                    );
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
        $requestedResources = $this->_getRequestedResources();
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

        $wsdlContent = $this->_autoDiscover->generate($resources, $this->_generateUri());

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
                $this->_soapServer
                    ->setWSDL($this->_getWsdlUrl())
                    ->setEncoding($this->_getApiCharset())
                    ->setClassmap($this->getApiConfig()->getTypeToClassMap());
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
        // Front controller plays the role of SOAP handler
        $this->_soapServer->setReturnResponse(true)->setObject($this);
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Handler_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->getResponse()->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object.
     *
     * @param string $responseBody
     * @return Mage_Webapi_Controller_Handler_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->getResponse()->setBody(
            preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
    }

    /**
     * Retrieve charset used in API.
     *
     * @return string
     */
    protected function _getApiCharset()
    {
        $charset = $this->_application->getStore()->getConfig(self::CONFIG_PATH_SOAP_CHARSET);
        return $charset ? $charset : self::SOAP_DEFAULT_ENCODING;
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
     * Get WSDL file URL.
     *
     * @return string
     */
    protected function _getWsdlUrl()
    {
        return $this->_generateUri(true);
    }

    /**
     * Get SOAP endpoint URL.
     *
     * @param bool $isWsdl
     * @return string
     */
    protected function _generateUri($isWsdl = false)
    {
        $params = array(
            self::REQUEST_PARAM_RESOURCES => $this->_getRequestedResources()
        );
        if ($isWsdl) {
            $params[self::REQUEST_PARAM_WSDL] = true;
        }
        $query = http_build_query($params, '', '&');
        return $this->_getEndpointUri() . '?' . $query;
    }

    /**
     * Generate URI of SOAP endpoint.
     *
     * @return string
     */
    protected function _getEndpointUri()
    {
        // @TODO: Implement proper endpoint URL retrieval mechanism in APIA-718 story
        return $this->_application->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
            . Mage_Webapi_Controller_Router_Route_Webapi::API_AREA_NAME . '/'
            . Mage_Webapi_Controller_Front::API_TYPE_SOAP;
    }

    /**
     * Generate SOAP fault.
     *
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param Exception $exception Exception can be used to add information to Detail node of SOAP message
     * @throws SoapFault
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _soapFault(
        $reason = self::FAULT_REASON_INTERNAL,
        $code = self::FAULT_CODE_RECEIVER,
        Exception $exception = null
    ) {
        header('Content-type: application/soap+xml; charset=UTF-8');
        if ($this->_isSoapExtensionLoaded()) {
            $details = null;
            if (!is_null($exception)) {
                $details = array('ExceptionCode' => $exception->getCode());
                // add detailed message only if it differs from fault reason
                if ($exception->getMessage() != $reason) {
                    $details['ExceptionMessage'] = $exception->getMessage();
                }
                if (Mage::getIsDeveloperMode()) {
                    $details['ExceptionTrace'] = "<![CDATA[{$exception->getTraceAsString()}]]>";
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
     * Generate SOAP fault message in XML format.
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param string $language Reason message language
     * @param string|array|null $details Detailed reason message(s)
     * @return string
     */
    protected function _getSoapFaultMessage(
        $reason = self::FAULT_REASON_INTERNAL,
        $code = self::FAULT_CODE_RECEIVER,
        $language = 'en',
        $details = null
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
     * Recursively convert details array into XML structure.
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
     * Check whether SOAP extension is loaded or not.
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
     * This method is required when there are two or more resource versions specified in request:
     * http://magento.host/api/soap?wsdl&resources[resource_a]=v1&resources[resource_b]=v2 <br/>
     * In this case it is not obvious what version of requested operation should be used.
     *
     * @param string $operationName
     * @return int
     * @throws Mage_Webapi_Exception
     */
    protected function _getOperationVersion($operationName)
    {
        $requestedResources = $this->_getRequestedResources();
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

    /**
     * Identify versions of resources that should be used for API configuration generation.
     *
     * @return array
     * @throws Mage_Webapi_Exception When GET parameters are invalid
     */
    protected function _getRequestedResources()
    {
        $wsdlParam = self::REQUEST_PARAM_WSDL;
        $resourcesParam = self::REQUEST_PARAM_RESOURCES;
        $requestParams = array_keys($this->getRequest()->getParams());
        $allowedParams = array('api_type', $wsdlParam, $resourcesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = $this->_helper->__('Not allowed parameters: %s. ', implode(', ', $notAllowedParameters))
                . $this->_helper->__('Please use only "%s" and "%s".', $wsdlParam, $resourcesParam);
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $requestedResources = $this->getRequest()->getParam($resourcesParam);
        if (empty($requestedResources) || !is_array($requestedResources) || empty($requestedResources)) {
            $message = $this->_helper->__('Requested resources are missing.');
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $requestedResources;
    }
}
