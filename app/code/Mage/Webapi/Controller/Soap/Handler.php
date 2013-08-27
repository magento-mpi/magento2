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
class Mage_Webapi_Controller_Soap_Handler
{
    const RESULT_NODE_NAME = 'result';

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Webapi_Controller_Soap_Security */
    protected $_security;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Mage_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Webapi_Model_Soap_Config */
    protected $_apiConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_App $application
     * @param Mage_Webapi_Controller_Soap_Request $request
     * @param Mage_Webapi_Controller_ErrorProcessor $errorProcessor
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Webapi_Model_Soap_Config $apiConfig
     * @param Mage_Webapi_Controller_Soap_Security $security
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Webapi_Controller_Soap_Request $request,
        Mage_Webapi_Controller_ErrorProcessor $errorProcessor,
        Magento_ObjectManager $objectManager,
        Mage_Webapi_Model_Soap_Config $apiConfig,
        Mage_Webapi_Controller_Soap_Security $security,
        Mage_Webapi_Helper_Data $helper
    ) {
        $this->_application = $application;
        $this->_request = $request;
        $this->_errorProcessor = $errorProcessor;
        $this->_objectManager = $objectManager;
        $this->_apiConfig = $apiConfig;
        $this->_security = $security;
        $this->_helper = $helper;
    }

    /**
     * Handler for all SOAP operations.
     *
     * @param string $operation
     * @param array $arguments
     *
     * @return stdClass
     * @throws Mage_Webapi_Exception
     */
    public function __call($operation, $arguments)
    {
        try {
            if ($this->_security->isSecurityHeader($operation)) {
                $this->_security->processSecurityHeader($operation, $arguments);
            } else {
                $this->_security->checkPermissions($operation, $arguments);
                $arguments = reset($arguments);
                $this->_unpackArguments($arguments);
                $arguments = get_object_vars($arguments);

                $requestedService = $this->_request->getRequestedServices();
                $serviceId = $this->_apiConfig->getClassBySoapOperation($operation, $requestedService);
                $serviceMethod = $this->_apiConfig->getMethodBySoapOperation($operation, $requestedService);

                // check if the operation is a secure operation & whether the request was made in HTTPS
                if ($this->_apiConfig->isSoapOperationSecure($operation, $requestedService)
                    && !$this->_request->isSecure()
                ) {
                    throw new Mage_Webapi_Exception(
                        "Operation allowed only in HTTPS", Mage_Webapi_Exception::HTTP_BAD_REQUEST);
                }

                $service = $this->_objectManager->get($serviceId);
                $outputData = $service->$serviceMethod($arguments);
                if (!is_array($outputData)) {
                    throw new LogicException(
                        $this->_helper->__('The method "%s" of service "%s" must return an array.', $serviceMethod,
                            $serviceId)
                    );
                }
                // TODO: Check why 'result' node is not generated in WSDL
                // return (object)array(self::RESULT_NODE_NAME => $outputData);
                return $outputData;
            }
        } catch (Exception $exception) {
            $this->_getException($exception);
        }
    }

    /**
     * Get Exception
     *
     * @param Exception $exception
     *
     * @throws Mage_Webapi_Model_Soap_Fault
     * @throws Mage_Webapi_Exception
     */
    protected function _getException($exception)
    {
        if ($exception instanceof Mage_Service_Exception) {
            $originator = Mage_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER;
            $parameters = $exception->getParameters();
        } elseif ($exception instanceof Mage_Webapi_Exception) {
            $originator = $exception->getOriginator();
        } else {
            $originator = Mage_Webapi_Model_Soap_Fault::FAULT_CODE_RECEIVER;
            $exception = $this->_errorProcessor->maskException($exception);
        }
        throw new Mage_Webapi_Model_Soap_Fault(
            $exception->getMessage(),
            $originator,
            $this->_application->getLocale()->getLocale()->getLanguage(),
            $exception,
            isset($parameters) ? $parameters : array(),
            $exception->getCode()
        );
    }

    /**
     * Go through an object parameters and unpack associative object to array.
     *
     * @param Object $obj - Link to Object
     * @return Object
     */
    protected function _unpackArguments(&$obj)
    {
        if (is_object($obj)) {
            if (property_exists($obj, 'key') && property_exists($obj, 'value')) {
                if (count(array_keys(get_object_vars($obj))) === 2) {
                    $obj = array($obj->key => $obj->value);
                    return true;
                }
            } else {
                foreach (array_keys(get_object_vars($obj)) as $key) {
                    $this->_unpackArguments($obj->$key);
                }
            }
        } else if (is_array($obj)) {
            $arr = array();
            $object = $obj;
            foreach ($obj as &$value) {
                if ($this->_unpackArguments($value)) {
                    array_walk($value, function($val, $key) use(&$arr) {
                        $arr[$key] = $val;
                    });
                    $object = $arr;
                }
            }
            $obj = $object;
        }
        return false;
    }
}
