<?php
/**
 * Dataservice factory
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Factory
{
    const DATASERVICE_PATH_SEPERATOR = '.';
    /**
     * @var Mage_Core_Model_Dataservice_Config_Interface
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Core_Model_Dataservice_Path_Composite */
    protected $_composite;

    /** @var Mage_Core_Model_Dataservice_Path_Navigator */
    protected $_pathNavigator;

    /**
     * @param Mage_Core_Model_Dataservice_Config_Interface $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Dataservice_Path_Composite $composite
     * @param Mage_Core_Model_Dataservice_Path_Navigator $pathNavigator
     */
    public function __construct(
        Mage_Core_Model_Dataservice_Config_Interface $config,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Dataservice_Path_Composite $composite,
        Mage_Core_Model_Dataservice_Path_Navigator $pathNavigator
    )
    {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_composite = $composite;
        $this->_pathNavigator = $pathNavigator;
    }

    /**
     * Init single service call
     *
     * @param $sourceName
     * @return bool|mixed
     */
    public function createDataservice($sourceName)
    {
        $classInformation = $this->getConfig()->getClassByAlias($sourceName);
        $instance = $this->_objectManager->create($classInformation['class']);
        $dataservice = $this->_applyMethod(
            $instance, $classInformation['retrieveMethod'],
            $classInformation['methodArguments']
        );
        return $dataservice;
    }

    /**
     * @return Mage_Core_Model_Dataservice_Config_Interface
     */
    public function getConfig()
    {
        return $this->_config;
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
        // convert from '{parent.child}' format to array('parent', 'child') format
        $pathArray = explode(self::DATASERVICE_PATH_SEPERATOR, trim($path, '{}'));
        return $this->_pathNavigator->search($this->_composite, $pathArray);
    }
}
