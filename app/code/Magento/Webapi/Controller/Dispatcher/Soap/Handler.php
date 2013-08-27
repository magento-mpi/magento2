<?php
/**
 * Handler of requests to SOAP server.
 *
 * The main responsibility is to instantiate proper action controller (resource) and execute requested method on it.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Soap_Handler
{
    const HEADER_SECURITY = 'Security';
    const RESULT_NODE_NAME = 'result';

    /** @var Magento_Webapi_Model_Config_Soap */
    protected $_apiConfig;

    /**
     * WS-Security UsernameToken object from request.
     *
     * @var stdClass
     */
    protected $_usernameToken;

    /** @var Magento_Webapi_Helper_Data */
    protected $_helper;

    /** @var Magento_Webapi_Controller_Dispatcher_Soap_Authentication */
    protected $_authentication;

    /**
     * Action controller factory.
     *
     * @var Magento_Webapi_Controller_Action_Factory
     */
    protected $_controllerFactory;

    /** @var Magento_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Magento_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Magento_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /**
     * List of headers passed in the request
     *
     * @var array
     */
    protected $_requestHeaders = array(self::HEADER_SECURITY);

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Config_Soap $apiConfig
     * @param Magento_Webapi_Helper_Data $helper
     * @param Magento_Webapi_Controller_Dispatcher_Soap_Authentication $authentication
     * @param Magento_Webapi_Controller_Action_Factory $controllerFactory
     * @param Magento_Webapi_Model_Authorization $authorization
     * @param Magento_Webapi_Controller_Request_Soap $request
     * @param Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     */
    public function __construct(
        Magento_Webapi_Model_Config_Soap $apiConfig,
        Magento_Webapi_Helper_Data $helper,
        Magento_Webapi_Controller_Dispatcher_Soap_Authentication $authentication,
        Magento_Webapi_Controller_Action_Factory $controllerFactory,
        Magento_Webapi_Model_Authorization $authorization,
        Magento_Webapi_Controller_Request_Soap $request,
        Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_helper = $helper;
        $this->_authentication = $authentication;
        $this->_controllerFactory = $controllerFactory;
        $this->_authorization = $authorization;
        $this->_request = $request;
        $this->_errorProcessor = $errorProcessor;
    }

    /**
     * Handler for all SOAP operations.
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass
     * @throws Magento_Webapi_Model_Soap_Fault
     * @throws Magento_Webapi_Exception
     */
    public function __call($operation, $arguments)
    {
        if (in_array($operation, $this->_requestHeaders)) {
            $this->_processSoapHeader($operation, $arguments);
        } else {
            try {
                if (is_null($this->_usernameToken)) {
                    throw new Magento_Webapi_Exception(
                        __('WS-Security UsernameToken is not found in SOAP-request.'),
                        Magento_Webapi_Exception::HTTP_UNAUTHORIZED
                    );
                }
                $this->_authentication->authenticate($this->_usernameToken);
                $resourceVersion = $this->_getOperationVersion($operation);
                $resourceName = $this->_apiConfig->getResourceNameByOperation($operation, $resourceVersion);
                if (!$resourceName) {
                    throw new Magento_Webapi_Exception(
                        __('Method "%1" is not found.', $operation),
                        Magento_Webapi_Exception::HTTP_NOT_FOUND
                    );
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
                return (object)array(self::RESULT_NODE_NAME => $outputData);
            } catch (Magento_Webapi_Exception $e) {
                throw new Magento_Webapi_Model_Soap_Fault($e->getMessage(), $e->getOriginator(), $e);
            } catch (Exception $e) {
                $maskedException = $this->_errorProcessor->maskException($e);
                throw new Magento_Webapi_Model_Soap_Fault(
                    $maskedException->getMessage(),
                    Magento_Webapi_Model_Soap_Fault::FAULT_CODE_RECEIVER,
                    $maskedException
                );
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
        $this->_requestHeaders = $requestHeaders;
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
     * Identify version of requested operation.
     *
     * This method is required when there are two or more resource versions specified in request:
     * http://magento.host/api/soap?wsdl&resources[resource_a]=v1&resources[resource_b]=v2 <br/>
     * In this case it is not obvious what version of requested operation should be used.
     *
     * @param string $operationName
     * @return int
     * @throws Magento_Webapi_Exception
     */
    protected function _getOperationVersion($operationName)
    {
        $requestedResources = $this->_request->getRequestedResources();
        $resourceName = $this->_apiConfig->getResourceNameByOperation($operationName);
        if (!isset($requestedResources[$resourceName])) {
            throw new Magento_Webapi_Exception(
                __('The version of "%1" operation cannot be identified.', $operationName),
                Magento_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        $version = (int)str_replace('V', '', ucfirst($requestedResources[$resourceName]));
        $this->_apiConfig->validateVersionNumber($version, $resourceName);
        return $version;
    }
}
