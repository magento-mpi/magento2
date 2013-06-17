<?php
/**
 * DataService invoker invokes the service, calls the methods and retrieves the data from the call.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Invoker
{
    /**
     * separates data structure hierarchy
     */
    const DATASERVICE_PATH_SEPARATOR = '.';

    /**
     * @var Mage_Core_Model_DataService_ConfigInterface
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Core_Model_DataService_Path_Composite */
    protected $_composite;

    /**
     * @param Mage_Core_Model_DataService_ConfigInterface $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_DataService_Path_Composite $composite
     */
    public function __construct(
        Mage_Core_Model_DataService_ConfigInterface $config,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_DataService_Path_Composite $composite
    ) {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_composite = $composite;
    }

    /**
     * Call service method and retrieve the data (array) from the call
     *
     * @param $sourceName
     * @throws InvalidArgumentException
     * @return bool|array
     */
    public function getServiceData($sourceName)
    {
        $classInformation = $this->_config->getClassByAlias($sourceName);
        $instance = $this->_objectManager->get($classInformation['class']);
        $serviceData = $this->_applyMethod(
            $instance, $classInformation['retrieveMethod'],
            $classInformation['methodArguments']
        );
        if (!is_array($serviceData)) {
            $type = gettype($serviceData);
            throw new InvalidArgumentException(
                "Data service method calls must return an array, received {$type} instead.
                 Called {$classInformation['class']}::{$classInformation['retrieveMethod']}"
            );
        }
        return $serviceData;
    }

    /**
     * Invoke method configured for service call
     *
     * @param $object
     * @param $methodName
     * @param $methodArguments
     * @return array
     */
    protected function _applyMethod($object, $methodName, $methodArguments)
    {
        $arguments = array();
        if (is_array($methodArguments)) {
            $arguments = $this->_prepareArguments($methodArguments);
        }
        return call_user_func_array(array($object, $methodName), $arguments);
    }

    /**
     * Prepare  values for the method params
     *
     * @param $argumentsList
     * @return array
     */
    protected function _prepareArguments($argumentsList)
    {
        $result = array();
        foreach ($argumentsList as $name => $value) {
            $result[$name] = $this->getArgumentValue($value);
        }
        return $result;
    }

    /**
     * Get the value for the method argument
     *
     * @param $path
     * @return null
     */
    public function getArgumentValue($path)
    {
        if (preg_match("/^\{\{.*\}\}$/", $path)) {
            // convert from '{{parent.child}}' format to array('parent', 'child') format
            $pathArray = explode(self::DATASERVICE_PATH_SEPARATOR, trim($path, '{}'));
            return Mage_Core_Model_DataService_Path_Navigator::search($this->_composite, $pathArray);
        }
        return $path;
    }
}
