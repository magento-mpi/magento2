<?php
/**
 * SOAP specific API config.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Soap
{
    /** @var Mage_Core_Service_Config */
    protected $_serviceConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Service_Config $serviceConfig
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(Mage_Core_Service_Config $serviceConfig, Mage_Webapi_Helper_Data $helper)
    {
        $this->_serviceConfig = $serviceConfig;
        $this->_helper = $helper;
    }

    /**
     * Identify resource name by operation name.
     *
     * @param string $operationName
     * @return string Resource name on success; false on failure
     * @throws Mage_Webapi_Exception In case when operation name is not valid
     */
    public function getServiceNameByOperation($operationName)
    {
        $serviceName = $this->getServiceNameByOperation($operationName);
        $methodName = $this->getMethodNameByOperation($operationName);
        try {
            $resourceData = $this->_serviceConfig->getServiceData($serviceName);
            $operationIsValid = isset($resourceData['methods'][$methodName]);
        } catch (LogicException $e) {
            $operationIsValid = false;
        }
        if (!$operationIsValid) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Operation "%s" is not found.', $operationName),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $serviceName;
    }

    /**
     * Identify method name by operation name.
     *
     * @param string $operationName
     * @return string|bool Method name on success; false on failure
     */
    public function getMethodNameByOperation($operationName)
    {
        list($serviceName, $methodName) = $this->parseOperationName($operationName);
        $serviceData = $this->_serviceConfig->getServiceData($serviceName);
        return isset($serviceData['methods'][$methodName]) ? $methodName : false;
    }

    /**
     * Parse operation name to separate resource name from method name.
     *
     * <pre>Result format:
     * array(
     *      0 => 'serviceName',
     *      1 => 'methodName'
     * )</pre>
     *
     * @param string $operationName
     * @return array
     * @throws InvalidArgumentException In case when the specified operation name is invalid.
     */
    public function parseOperationName($operationName)
    {
        /** Note that '(.*?)' must not be greedy to allow regexp to match 'multiUpdate' method before 'update' */
        $regEx = sprintf('/(%s)(.*?)$/i', implode('|', $this->_serviceConfig->getResourcesNames()));
        if (preg_match($regEx, $operationName, $matches)) {
            $serviceName = $matches[1];
            $methodName = lcfirst($matches[2]);
            $result = array($serviceName, $methodName);
            return $result;
        }
        throw new InvalidArgumentException(sprintf(
            'The "%s" is not a valid API resource operation name.',
            $operationName
        ));
    }
}
