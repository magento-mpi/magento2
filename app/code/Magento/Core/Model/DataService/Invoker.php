<?php
/**
 * DataService invoker invokes the service, calls the methods and retrieves the data from the call.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Invoker
{
    /**
     * separates data structure hierarchy
     */
    const DATASERVICE_PATH_SEPARATOR = '.';

    /**
     * @var Magento_Core_Model_DataService_ConfigInterface
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Magento_Core_Model_DataService_Path_Composite */
    protected $_composite;

    /**
     * @var Magento_Core_Model_DataService_Path_Navigator
     */
    private $_navigator;

    /**
     * @param Magento_Core_Model_DataService_ConfigInterface $config
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_DataService_Path_Composite $composite
     * @param Magento_Core_Model_DataService_Path_Navigator $navigator
     */
    public function __construct(
        Magento_Core_Model_DataService_ConfigInterface $config,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_DataService_Path_Composite $composite,
        Magento_Core_Model_DataService_Path_Navigator $navigator
    ) {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_composite = $composite;
        $this->_navigator = $navigator;
    }

    /**
     * Call service method and retrieve the data (array) from the call
     *
     * @param string $sourceName
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
     * @param Object $object
     * @param string $methodName
     * @param array $methodArguments
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
     * @param array $argumentsList
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
     * @param string $valueTemplate
     * @return mixed
     */
    public function getArgumentValue($valueTemplate)
    {
        $composite = $this->_composite;
        $navigator = $this->_navigator;
        $callback = function ($matches) use ($composite, $navigator) {
            // convert from '{{parent.child}}' format to array('parent', 'child') format
            $pathArray = explode(Magento_Core_Model_DataService_Invoker::DATASERVICE_PATH_SEPARATOR, $matches[1]);
            return $navigator->search($composite, $pathArray);
        };

        return preg_replace_callback('(\{\{(.*?)\}\})', $callback, $valueTemplate);
    }
}
