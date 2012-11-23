<?php
/**
 * Dispatcher for SOAP API calls.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_Soap extends Mage_Webapi_Controller_DispatcherAbstract
{
    const WEBSERVICE_CACHE_NAME = 'config_webservice';
    const WEBSERVICE_CACHE_TAG = 'WEBSERVICE';
    const WSDL_CACHE_ID = 'WSDL';

    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Core_Model_Cache */
    protected $_cache;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config $apiConfig
     * @param Mage_Webapi_Controller_Request_Soap $request
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover
     * @param Mage_Webapi_Model_Soap_Server $soapServer
     * @param Mage_Core_Model_App $application
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Webapi_Model_Soap_Fault $soapFault
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Mage_Webapi_Model_Soap_Server $soapServer,
        Mage_Core_Model_App $application,
        Mage_Core_Model_Cache $cache,
        Mage_Webapi_Model_Soap_Fault $soapFault
    ) {
        parent::__construct(
            $helper,
            $apiConfig,
            $response
        );
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_cache = $cache;
        $this->_application = $application;
        $this->_request = $request;
        $this->_soapFault = $soapFault;
    }

    /**
     * Dispatcher for all SOAP operations.
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
            try {
                if (is_null($this->_usernameToken)) {
                    $this->_soapFault(
                        $this->_helper->__('WS-Security UsernameToken is not found in SOAP-request.'),
                        self::FAULT_CODE_RECEIVER
                    );
                }
                $this->_authentication->authenticate($this->_usernameToken);
                $resourceVersion = $this->_getOperationVersion($operation);
                $resourceName = $this->getApiConfig()->getResourceNameByOperation($operation, $resourceVersion);
                if (!$resourceName) {
                    $this->_soapFault(sprintf('Method "%s" is not found.', $operation), self::FAULT_CODE_SENDER);
                }
                $controllerClass = $this->getApiConfig()->getControllerClassByOperationName($operation);
                $controllerInstance = $this->_controllerFactory->createActionController(
                    $controllerClass,
                     $this->_request
                 );
                $method = $this->getApiConfig()->getMethodNameByOperation($operation, $resourceVersion);

                $this->_authorization->checkResourceAcl($resourceName, $method);

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
                // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
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
     * Implementation of abstract method.
     *
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    public function init()
    {
        parent::init();
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    public function dispatch()
    {
        try {
            if ($this->_request->getParam(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null) {
                $this->_setResponseContentType('text/xml');
                $responseBody = $this->_getWsdlContent();
            } else {
                $this->_setResponseContentType('application/soap+xml');
                $responseBody = $this->_soapServer->handle();
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
                        $this->_soapServer->getEndpointUri(),
                        $resourceName,
                        $version
                    );
                }
            }
        }
        $this->_setResponseBody(
            $this->_soapFault->getSoapFaultMessage(
                $message,
                Mage_Webapi_Controller_Dispatcher_Soap_Handler::FAULT_CODE_SENDER,
                'en',
                $details
            )
        );
    }

    /**
     * Generate WSDL content based on resource config.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _getWsdlContent()
    {
        $requestedResources = $this->_request->getRequestedResources();
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

        $wsdlContent = $this->_autoDiscover->generate($resources, $this->_soapServer->generateUri());

        if ($this->_cache->canUse(self::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save($wsdlContent, $cacheId, array(self::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->getResponse()->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_soapServer->getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object.
     *
     * @param string $responseBody
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->getResponse()->setBody(
            preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_soapServer->getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
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
                // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
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
        $requestedResources = $this->_request->getRequestedResources();
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
