<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Test_TestCase_ObjectManagerAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Get block instance
     *
     * @param string $className
     * @param array $data
     * @return Mage_Core_Block_Abstract
     */
    public function getBlock($className, array $data = array())
    {
        $params = array(
            'request'         => $this->_getMockWithoutConstructorCall('Mage_Core_Controller_Request_Http'),
            'layout'          => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Layout'),
            'eventManager'    => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Event_Manager'),
            'translator'      => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Translate'),
            'cache'           => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Cache'),
            'designPackage'   => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Design_Package'),
            'session'         => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Session'),
            'storeConfig'     => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Store_Config'),
            'frontController' => $this->_getMockWithoutConstructorCall('Mage_Core_Controller_Varien_Front')
        );

        $params = array_merge($params, $data);

        return $this->_getInstanceViaConstructor($className, $params);
    }

    /**
     * Get model instance
     *
     * @param string $className
     * @param array $data
     * @return Mage_Core_Model_Abstract
     */
    public function getModel($className, array $data = array())
    {
        $params = array_merge($this->_getArgumentsForModel($className), $data);
        return $this->_getInstanceViaConstructor($className, $params);
    }

    /**
     * Retrieve list of arguments that used for new model instance creation
     *
     * @param string $className
     * @return array
     */
    protected function _getArgumentsForModel($className = '')
    {
        /** @var $resourceMock Mage_Core_Model_Resource_Resource */
        $resourceMock = $this->getMock('Mage_Core_Model_Resource_Resource', array('getIdFieldName'),
            array(), '', false
        );
        $resourceMock->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('id'));

        $arguments = array(
            'eventDispatcher'    => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Event_Manager'),
            'cacheManager'       => $this->_getMockWithoutConstructorCall('Mage_Core_Model_Cache'),
            'resource'           => $resourceMock,
            'resourceCollection' => $this->_getMockWithoutConstructorCall('Varien_Data_Collection_Db'),
        );

        if ($className) {
            return $this->_sortConstructorArguments($className, $arguments);
        } else {
            return $arguments;
        }
    }

    /**
     * Sort constructor arguments array as is defined for current class interface
     *
     * @param string $className
     * @param array $arguments
     * @return array
     */
    protected function _sortConstructorArguments($className, array $arguments)
    {
        $constructArguments = array();
        $method = new ReflectionMethod($className, '__construct');
        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            if (isset($arguments[$parameterName])) {
                $constructArguments[$parameterName] = $arguments[$parameterName];
            } else {
                if ($parameter->isDefaultValueAvailable()) {
                    $constructArguments[$parameterName] = $parameter->getDefaultValue();
                } else {
                    $constructArguments[$parameterName] = null;
                }
            }
        }

        return $constructArguments;
    }

    /**
     * Get mock without call of original constructor
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockWithoutConstructorCall($className)
    {
        return $this->getMock($className, array(), array(), '', false);
    }

    /**
     * Get class instance via constructor
     *
     * @param string $className
     * @param array $arguments
     * @return object
     */
    protected function _getInstanceViaConstructor($className, array $arguments = array())
    {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($arguments);
    }
}
