<?php
/**
 * Handler of requests to SOAP server.
 *
 * The main responsibility is to instantiate proper action controller (resource) and execute requested method on it.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_Soap_Handler
{
    const FAULT_REASON_INTERNAL = 'Internal Error.';

    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';
    const HEADER_SECURITY = 'Security';

    /** @var Mage_Webapi_Model_Config */
    protected $_apiConfig;

    /**
     * WS-Security UsernameToken object from request.
     *
     * @var stdClass
     */
    protected $_usernameToken;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Webapi_Controller_Dispatcher_Soap_Authentication */
    protected $_authentication;

    /**
     * Action controller factory.
     *
     * @var Mage_Webapi_Controller_Action_Factory
     */
    protected $_controllerFactory;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    /** @var Mage_Core_Model_Logger */
    protected $_logger;

    /**
     * List of headers passed in the request
     *
     * @var array
     */
    protected $_requestHeaders = array(self::HEADER_SECURITY);

    public function __construct(
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Controller_Dispatcher_Soap_Authentication $authentication,
        Mage_Webapi_Controller_Action_Factory $controllerFactory,
        Mage_Webapi_Model_Authorization $authorization,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Model_Soap_Fault $soapFault,
        Mage_Core_Model_Logger $logger
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_helper = $helper;
        $this->_authentication = $authentication;
        $this->_controllerFactory = $controllerFactory;
        $this->_authorization = $authorization;
        $this->_request = $request;
        $this->_soapFault = $soapFault;
        $this->_logger = $logger;
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
        if (in_array($operation, $this->_requestHeaders)) {
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
                $resourceName = $this->_apiConfig->getResourceNameByOperation($operation, $resourceVersion);
                if (!$resourceName) {
                    $this->_soapFault(sprintf('Method "%s" is not found.', $operation), self::FAULT_CODE_SENDER);
                }
                $controllerClass = $this->_apiConfig->getControllerClassByOperationName($operation);
                $controllerInstance = $this->_controllerFactory->createActionController(
                    $controllerClass,
                    $this->_request
                );
                $method = $this->_apiConfig->getMethodNameByOperation($operation, $resourceVersion);

                $this->_authorization->checkResourceAcl($resourceName, $method);

                $arguments = reset($arguments);
                $arguments = get_object_vars($arguments);
                $versionAfterFallback = $this->_apiConfig->identifyVersionSuffix(
                    $operation,
                    $resourceVersion,
                    $controllerInstance
                );
                $this->_apiConfig->checkDeprecationPolicy($resourceName, $method, $versionAfterFallback);
                $action = $method . $versionAfterFallback;
                $arguments = $this->_helper->prepareMethodParams(
                    $controllerClass,
                    $action,
                    $arguments,
                    $this->_apiConfig
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
     * Set request headers
     *
     * @param array $requestHeaders
     */
    public function setRequestHeaders(array $requestHeaders)
    {
        $this->_requestHeaders;
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
            case self::HEADER_SECURITY:
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
            die($this->_soapFault->getSoapFaultMessage($reason, $code, $language, $details));
        } else {
            die($this->_soapFault->getSoapFaultMessage(self::FAULT_CODE_RECEIVER, 'SOAP extension is not loaded.'));
        }
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
        $resourceName = $this->_apiConfig->getResourceNameByOperation($operationName);
        if (!isset($requestedResources[$resourceName])) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('The version of "%s" operation cannot be identified.', $operationName),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        $version = (int)str_replace('V', '', ucfirst($requestedResources[$resourceName]));
        $this->_apiConfig->validateVersionNumber($version, $resourceName);
        return $version;
    }
}
