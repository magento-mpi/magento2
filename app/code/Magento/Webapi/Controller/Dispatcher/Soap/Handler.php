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
namespace Magento\Webapi\Controller\Dispatcher\Soap;

class Handler
{
    const HEADER_SECURITY = 'Security';
    const RESULT_NODE_NAME = 'result';

    /** @var \Magento\Webapi\Model\Config\Soap */
    protected $_apiConfig;

    /**
     * WS-Security UsernameToken object from request.
     *
     * @var \stdClass
     */
    protected $_usernameToken;

    /** @var \Magento\Webapi\Helper\Data */
    protected $_helper;

    /** @var \Magento\Webapi\Controller\Dispatcher\Soap\Authentication */
    protected $_authentication;

    /**
     * Action controller factory.
     *
     * @var \Magento\Webapi\Controller\Action\Factory
     */
    protected $_controllerFactory;

    /** @var \Magento\Webapi\Model\Authorization */
    protected $_authorization;

    /** @var \Magento\Webapi\Controller\Request\Soap */
    protected $_request;

    /** @var \Magento\Webapi\Controller\Dispatcher\ErrorProcessor */
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
     * @param \Magento\Webapi\Model\Config\Soap $apiConfig
     * @param \Magento\Webapi\Helper\Data $helper
     * @param \Magento\Webapi\Controller\Dispatcher\Soap\Authentication $authentication
     * @param \Magento\Webapi\Controller\Action\Factory $controllerFactory
     * @param \Magento\Webapi\Model\Authorization $authorization
     * @param \Magento\Webapi\Controller\Request\Soap $request
     * @param \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Soap $apiConfig,
        \Magento\Webapi\Helper\Data $helper,
        \Magento\Webapi\Controller\Dispatcher\Soap\Authentication $authentication,
        \Magento\Webapi\Controller\Action\Factory $controllerFactory,
        \Magento\Webapi\Model\Authorization $authorization,
        \Magento\Webapi\Controller\Request\Soap $request,
        \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor
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
     * @return \stdClass
     * @throws \Magento\Webapi\Model\Soap\Fault
     * @throws \Magento\Webapi\Exception
     */
    public function __call($operation, $arguments)
    {
        if (in_array($operation, $this->_requestHeaders)) {
            $this->_processSoapHeader($operation, $arguments);
        } else {
            try {
                if (is_null($this->_usernameToken)) {
                    throw new \Magento\Webapi\Exception(
                        __('WS-Security UsernameToken is not found in SOAP-request.'),
                        \Magento\Webapi\Exception::HTTP_UNAUTHORIZED
                    );
                }
                $this->_authentication->authenticate($this->_usernameToken);
                $resourceVersion = $this->_getOperationVersion($operation);
                $resourceName = $this->_apiConfig->getResourceNameByOperation($operation, $resourceVersion);
                if (!$resourceName) {
                    throw new \Magento\Webapi\Exception(
                        __('Method "%1" is not found.', $operation),
                        \Magento\Webapi\Exception::HTTP_NOT_FOUND
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
            } catch (\Magento\Webapi\Exception $e) {
                throw new \Magento\Webapi\Model\Soap\Fault($e->getMessage(), $e->getOriginator(), $e);
            } catch (\Exception $e) {
                $maskedException = $this->_errorProcessor->maskException($e);
                throw new \Magento\Webapi\Model\Soap\Fault(
                    $maskedException->getMessage(),
                    \Magento\Webapi\Model\Soap\Fault::FAULT_CODE_RECEIVER,
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
     * @throws \Magento\Webapi\Exception
     */
    protected function _getOperationVersion($operationName)
    {
        $requestedResources = $this->_request->getRequestedResources();
        $resourceName = $this->_apiConfig->getResourceNameByOperation($operationName);
        if (!isset($requestedResources[$resourceName])) {
            throw new \Magento\Webapi\Exception(
                __('The version of "%1" operation cannot be identified.', $operationName),
                \Magento\Webapi\Exception::HTTP_NOT_FOUND
            );
        }
        $version = (int)str_replace('V', '', ucfirst($requestedResources[$resourceName]));
        $this->_apiConfig->validateVersionNumber($version, $resourceName);
        return $version;
    }
}
