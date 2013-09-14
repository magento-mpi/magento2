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
class Magento_Webapi_Controller_Soap_Handler
{
    const RESULT_NODE_NAME = 'result';

    /** @var Magento_Core_Model_App */
    protected $_application;

    /** @var Magento_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Magento_Webapi_Model_Soap_Config */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Model_App $application
     * @param Magento_Webapi_Controller_Soap_Request $request
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Webapi_Model_Soap_Config $apiConfig
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Webapi_Controller_Soap_Request $request,
        Magento_ObjectManager $objectManager,
        Magento_Webapi_Model_Soap_Config $apiConfig
    ) {
        $this->_application = $application;
        $this->_request = $request;
        $this->_objectManager = $objectManager;
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Handler for all SOAP operations.
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass|null
     * @throws Magento_Webapi_Exception|LogicException
     */
    public function __call($operation, $arguments)
    {
        $requestedServices = $this->_request->getRequestedServices();
        $serviceMethodInfo = $this->_apiConfig->getServiceMethodInfo($operation, $requestedServices);
        $serviceId = $serviceMethodInfo[Magento_Webapi_Model_Soap_Config::KEY_CLASS];
        $serviceMethod = $serviceMethodInfo[Magento_Webapi_Model_Soap_Config::KEY_METHOD];

        // check if the operation is a secure operation & whether the request was made in HTTPS
        if ($serviceMethodInfo[Magento_Webapi_Model_Soap_Config::KEY_IS_SECURE] && !$this->_request->isSecure()) {
            throw new Magento_Webapi_Exception(__("Operation allowed only in HTTPS"));
        }

        $service = $this->_objectManager->get($serviceId);
        $outputData = $service->$serviceMethod($this->_prepareParameters($arguments));
        if (!is_array($outputData)) {
            throw new LogicException(
                sprintf('The method "%s" of service "%s" must return an array.', $serviceMethod, $serviceId)
            );
        }
        return $outputData;
    }

    /**
     * Extract service method parameters from SOAP operation arguments.
     *
     * @param stdClass|array $arguments
     * @return array
     */
    protected function _prepareParameters($arguments)
    {
        /** SoapServer wraps parameters into array. Thus this wrapping should be removed to get access to parameters. */
        $arguments = reset($arguments);
        $this->_associativeObjectToArray($arguments);
        $arguments = get_object_vars($arguments);
        return $arguments;
    }

    /**
     * Go through an object parameters and unpack associative object to array.
     *
     * This function uses recursion and operates by reference.
     *
     * @param stdClass|array $obj
     * @return bool
     */
    protected function _associativeObjectToArray(&$obj)
    {
        if (is_object($obj)) {
            if (property_exists($obj, 'key') && property_exists($obj, 'value')) {
                if (count(array_keys(get_object_vars($obj))) === 2) {
                    $obj = array($obj->key => $obj->value);
                    return true;
                }
            } else {
                foreach (array_keys(get_object_vars($obj)) as $key) {
                    $this->_associativeObjectToArray($obj->$key);
                }
            }
        } else if (is_array($obj)) {
            $arr = array();
            $object = $obj;
            foreach ($obj as &$value) {
                if ($this->_associativeObjectToArray($value)) {
                    array_walk($value, function ($val, $key) use (&$arr) {
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
