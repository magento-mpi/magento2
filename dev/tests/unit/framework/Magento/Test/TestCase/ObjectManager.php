<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_TestCase_ObjectManager extends PHPUnit_Framework_TestCase
{
    /**
     * Get block instance
     *
     * @param string $className
     * @param array $data
     * @return object
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

        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($params);
    }

    /**
     * Get mock without call of original constructor
     *
     * @param $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockWithoutConstructorCall($className)
    {
        return $this->getMock($className, array(), array(), '', false);
    }
}
