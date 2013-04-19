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
class Mage_Webapi_Controller_Dispatcher_Soap_Handler
{
    const HEADER_SECURITY = 'Security';
    const RESULT_NODE_NAME = 'result';

    /** @var Mage_Core_Service_Config */
    protected $_serviceConfig;

    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_soapApiConfig;

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
     * @var Mage_Core_Service_Factory
     */
    protected $_serviceFactory;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
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
     * @param Mage_Core_Service_Config $serviceConfig
     * @param Mage_Webapi_Model_Config_Soap $soapApiConfig
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Controller_Dispatcher_Soap_Authentication $authentication
     * @param Mage_Core_Service_Factory $controllerFactory
     * @param Mage_Webapi_Model_Authorization $authorization
     * @param Mage_Webapi_Controller_Request_Soap $request
     * @param Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     */
    public function __construct(
        Mage_Core_Service_Config $serviceConfig,
        Mage_Webapi_Model_Config_Soap $soapApiConfig,
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Controller_Dispatcher_Soap_Authentication $authentication,
        Mage_Core_Service_Factory $controllerFactory,
        Mage_Webapi_Model_Authorization $authorization,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
    ) {
        $this->_serviceConfig = $serviceConfig;
        $this->_soapApiConfig = $soapApiConfig;
        $this->_helper = $helper;
        $this->_authentication = $authentication;
        $this->_serviceFactory = $controllerFactory;
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
     * @throws Mage_Webapi_Model_Soap_Fault
     * @throws Mage_Webapi_Exception
     */
    public function __call($operation, $arguments)
    {
        if (in_array($operation, $this->_requestHeaders)) {
            $this->_processSoapHeader($operation, $arguments);
        } else {
            try {
                if (is_null($this->_usernameToken)) {
                    throw new Mage_Webapi_Exception(
                        $this->_helper->__('WS-Security UsernameToken is not found in SOAP-request.'),
                        Mage_Webapi_Exception::HTTP_UNAUTHORIZED
                    );
                }
                $this->_authentication->authenticate($this->_usernameToken);
                $serviceName = $this->_soapApiConfig->getServiceNameByOperation($operation);
                $serviceInstance = $this->_serviceFactory->createServiceInstance($serviceName);
                $method = $this->_soapApiConfig->getMethodNameByOperation($operation);

                /**
                 * TODO: Uncomment authorization check after it is refactored.
                 * TODO: Uncomment its testing in Mage_Webapi_Controller_Dispatcher_Soap_HandlerTest::testCall()
                 */
                // $this->_authorization->checkResourceAcl($serviceName, $method);

                $arguments = reset($arguments);
                $arguments = get_object_vars($arguments);

                $this->_serviceConfig->checkDeprecationPolicy($serviceName, $method);
                // TODO: Refactor after versioning removal
                $action = $method;
                $arguments = $this->_helper->prepareMethodParams(
                    $serviceName,
                    $action,
                    $arguments,
                    $this->_serviceConfig
                );
                $outputData = call_user_func_array(array($serviceInstance, $action), $arguments);
                return (object)array(self::RESULT_NODE_NAME => $outputData);
            } catch (Mage_Webapi_Exception $e) {
                throw new Mage_Webapi_Model_Soap_Fault($e->getMessage(), $e->getOriginator(), $e);
            } catch (Exception $e) {
                $maskedException = $this->_errorProcessor->maskException($e);
                throw new Mage_Webapi_Model_Soap_Fault(
                    $maskedException->getMessage(),
                    Mage_Webapi_Model_Soap_Fault::FAULT_CODE_RECEIVER,
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
}
