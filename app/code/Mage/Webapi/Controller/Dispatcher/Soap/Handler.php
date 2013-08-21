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
    const RESULT_NODE_NAME = 'result';

    /** @var Mage_Webapi_Controller_Dispatcher_Soap_Security */
    protected $_security;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_newApiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Request_Soap $request
     * @param Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Webapi_Model_Config_Soap $newApiConfig
     * @param Mage_Webapi_Controller_Dispatcher_Soap_Security $security
     */
    public function __construct(
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor,
        Magento_ObjectManager $objectManager,
        Mage_Webapi_Model_Config_Soap $newApiConfig,
        Mage_Webapi_Controller_Dispatcher_Soap_Security $security
    ) {
        $this->_request = $request;
        $this->_errorProcessor = $errorProcessor;
        $this->_objectManager = $objectManager;
        $this->_newApiConfig = $newApiConfig;
        $this->_security = $security;
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
            $exception,
            isset($parameters) ? $parameters : array(),
            $exception->getCode()
        );
    }

    /**
     * TODO: Refactor method, which was copied from Mage_Api module
     * Go through an object parameters and unpack associative object to array.
     *
     * @param Object $obj - Link to Object
     * @return Object
     * TODO: Remove warning suppression after simplifying the method
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _unpackArguments(&$obj)
    {
        if (is_object($obj) && property_exists($obj, 'key') && property_exists($obj, 'value')) {
            if (count(array_keys(get_object_vars($obj))) == 2) {
                $obj = array($obj->key => $obj->value);
                return true;
            }
        } elseif (is_array($obj)) {
            $arr = array();
            $needReplacement = true;
            foreach ($obj as &$value) {
                $isAssoc = $this->_unpackArguments($value);
                if ($isAssoc) {
                    foreach ($value as $aKey => $aVal) {
                        $arr[$aKey] = $aVal;
                    }
                } else {
                    $needReplacement = false;
                }
            }
            if ($needReplacement) {
                $obj = $arr;
            }
        } elseif (is_object($obj)) {
            $objectKeys = array_keys(get_object_vars($obj));

            foreach ($objectKeys as $key) {
                $this->_unpackArguments($obj->$key);
            }
        }
        return false;
    }
}
