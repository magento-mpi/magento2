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
namespace Magento\Webapi\Controller\Soap;

class Handler
{
    const RESULT_NODE_NAME = 'result';

    /** @var \Magento\Core\Model\App */
    protected $_application;

    /** @var \Magento\Webapi\Controller\Soap\Request */
    protected $_request;

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\Webapi\Model\Soap\Config */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Webapi\Controller\Soap\Request $request
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Webapi\Model\Soap\Config $apiConfig
     */
    public function __construct(
        \Magento\Core\Model\App $application,
        \Magento\Webapi\Controller\Soap\Request $request,
        \Magento\ObjectManager $objectManager,
        \Magento\Webapi\Model\Soap\Config $apiConfig
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
     * @throws \Magento\Webapi\Exception|LogicException
     */
    public function __call($operation, $arguments)
    {
        $requestedServices = $this->_request->getRequestedServices();
        $serviceMethodInfo = $this->_apiConfig->getServiceMethodInfo($operation, $requestedServices);
        $serviceId = $serviceMethodInfo[\Magento\Webapi\Model\Soap\Config::KEY_CLASS];
        $serviceMethod = $serviceMethodInfo[\Magento\Webapi\Model\Soap\Config::KEY_METHOD];

        // check if the operation is a secure operation & whether the request was made in HTTPS
        if ($serviceMethodInfo[\Magento\Webapi\Model\Soap\Config::KEY_IS_SECURE] && !$this->_request->isSecure()) {
            throw new \Magento\Webapi\Exception(__("Operation allowed only in HTTPS"));
        }

        $service = $this->_objectManager->get($serviceId);
        $outputData = $service->$serviceMethod($this->_prepareParameters($arguments));
        if (!is_array($outputData)) {
            throw new \LogicException(
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
