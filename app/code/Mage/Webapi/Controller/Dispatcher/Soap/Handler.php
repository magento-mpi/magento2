<?php
/**
 * Handler of requests to SOAP server.
 *
 * The main responsibility is to instantiate proper action controller (service) and execute requested method on it.
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

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Webapi_Config */
    protected $_newApiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Controller_Dispatcher_Soap_Authentication $authentication
     * @param Mage_Webapi_Model_Authorization $authorization
     * @param Mage_Webapi_Controller_Request_Soap $request
     * @param Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Webapi_Config $newApiConfig
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Controller_Dispatcher_Soap_Authentication $authentication,
        Mage_Webapi_Model_Authorization $authorization,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor,
        Magento_ObjectManager $objectManager,
        Mage_Webapi_Config $newApiConfig
    ) {
        $this->_helper = $helper;
        $this->_authentication = $authentication;
        $this->_authorization = $authorization;
        $this->_request = $request;
        $this->_errorProcessor = $errorProcessor;
        $this->_objectManager = $objectManager;
        $this->_newApiConfig = $newApiConfig;
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
                // TODO: Uncomment authentication
//                if (is_null($this->_usernameToken)) {
//                    throw new Mage_Webapi_Exception(
//                        $this->_helper->__('WS-Security UsernameToken is not found in SOAP-request.'),
//                        Mage_Webapi_Exception::HTTP_UNAUTHORIZED
//                    );
//                }
//                $this->_authentication->authenticate($this->_usernameToken);

                // TODO: Enable authorization
//                $this->_authorization->checkServiceAcl($serviceName, $method);

                $arguments = reset($arguments);
                $arguments = get_object_vars($arguments);

                $requestedService = $this->_request->getRequestedServices();
                $serviceId = $this->_newApiConfig->getClassBySoapOperation($operation, $requestedService);
                $serviceMethod = $this->_newApiConfig->getMethodBySoapOperation($operation, $requestedService);

                // check if the operation is a secure operation & whether the request was made in HTTPS
                if ($this->_newApiConfig->isSoapOperationSecure($operation, $requestedService)
                    && !$this->_request->isSecure()
                ) {
                    // TODO: Set the right error code and replace generic Exception with right exception instance
                    throw new Mage_Webapi_Exception("Operation allowed only in HTTPS", 4000);
                }

                $service = $this->_objectManager->get($serviceId);
                   $outputData = $service->$serviceMethod($arguments);
                if ($outputData instanceof Varien_Object || $outputData instanceof Varien_Data_Collection_Db) {
                    $outputData = $outputData->getData();
                }
                // TODO: Check why 'result' node is not generated in WSDL
                // return (object)array(self::RESULT_NODE_NAME => $outputData);
                return $outputData;
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
