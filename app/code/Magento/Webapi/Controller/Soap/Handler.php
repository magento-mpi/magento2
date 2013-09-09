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

    /** @var Magento_Webapi_Controller_Soap_Security */
    protected $_security;

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
     * @param Magento_Webapi_Controller_Soap_Security $security
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Webapi_Controller_Soap_Request $request,
        Magento_ObjectManager $objectManager,
        Magento_Webapi_Model_Soap_Config $apiConfig,
        Magento_Webapi_Controller_Soap_Security $security
    ) {
        $this->_application = $application;
        $this->_request = $request;
        $this->_objectManager = $objectManager;
        $this->_apiConfig = $apiConfig;
        $this->_security = $security;
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
        if ($this->_security->isSecurityHeader($operation)) {
            $this->_security->processSecurityHeader($operation, $arguments);
        } else {
            $this->_security->checkPermissions($operation, $arguments);
            $arguments = reset($arguments);
            $this->_unpackArguments($arguments);
            $arguments = get_object_vars($arguments);

            $requestedServices = $this->_request->getRequestedServices();
            $serviceMethodInfo = $this->_apiConfig->getServiceMethodInfo($operation, $requestedServices);
            $serviceId = $serviceMethodInfo[Magento_Webapi_Model_Soap_Config::KEY_CLASS];
            $serviceMethod = $serviceMethodInfo[Magento_Webapi_Model_Soap_Config::KEY_METHOD];

            // check if the operation is a secure operation & whether the request was made in HTTPS
            if ($serviceMethodInfo[Magento_Webapi_Model_Soap_Config::KEY_IS_SECURE] && !$this->_request->isSecure()) {
                throw new Magento_Webapi_Exception(
                    __("Operation allowed only in HTTPS"),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }

            $service = $this->_objectManager->get($serviceId);
            $outputData = $service->$serviceMethod($arguments);
            if (!is_array($outputData)) {
                throw new LogicException(
                    sprintf('The method "%s" of service "%s" must return an array.', $serviceMethod, $serviceId)
                );
            }
            // TODO: Check why 'result' node is not generated in WSDL
            // return (object)array(self::RESULT_NODE_NAME => $outputData);
            return $outputData;
        }
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
