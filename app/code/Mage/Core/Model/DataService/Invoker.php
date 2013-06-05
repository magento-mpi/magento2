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

    /** @var Mage_Core_Model_DataService_Path_Navigator */
    protected $_pathNavigator;

    /**
     * @param Mage_Core_Model_DataService_ConfigInterface $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_DataService_Path_Composite $composite
     * @param Mage_Core_Model_DataService_Path_Navigator $pathNavigator
     */
    public function __construct(
        Mage_Core_Model_DataService_ConfigInterface $config,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_DataService_Path_Composite $composite,
        Mage_Core_Model_DataService_Path_Navigator $pathNavigator
    ) {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_composite = $composite;
        $this->_pathNavigator = $pathNavigator;
    }

    /**
     * Call service method and retrieve the data from the call
     *
     * @param $sourceName
     * @return bool|mixed
     */
    public function getServiceData($sourceName)
    {
        $classInformation = $this->_config->getClassByAlias($sourceName);
        $instance = $this->_objectManager->get($classInformation['class']);
        $serviceData = $this->_applyMethod(
            $instance, $classInformation['retrieveMethod'],
            $classInformation['methodArguments']
        );
        return $serviceData;
    }

    /**
     * Invoke method configured for service call
     *
     * @param $object
     * @param $methodName
     * @param $methodArguments
     * @return mixed
     */
    protected function _applyMethod($object, $methodName, $methodArguments)
    {
        $result = null;
        $arguments = array();
        if (is_array($methodArguments)) {
            $arguments = $this->_prepareArguments($methodArguments);
        }
        $result = call_user_func_array(array($object, $methodName), $arguments);
        return $result;
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
            // convert from '{parent.child}' format to array('parent', 'child') format
            $pathArray = explode(self::DATASERVICE_PATH_SEPARATOR, trim($value, '{}'));
            $result[$name] = $this->_pathNavigator->search($this->_composite, $pathArray);
        }
        return $result;
    }
}
