<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Service_ServiceAbstract
 */
class Mage_Core_Service_ServiceAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Service_ServiceAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_service;

    /**
     * Initialize service abstract for testing
     */
    protected function setUp()
    {
        $config = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = $this->getMockBuilder('Mage_Core_Helper_Data')
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $this->_service = $this->getMockBuilder('Mage_Core_Service_ServiceAbstract')
            ->setConstructorArgs(array(array(
                'config' => $config,
                'helper' => $helper,
            )))
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->_service);
    }

    /**
     * Test for _setDataUsingMethods method
     */
    public function testSetDataUsingMethods()
    {
        /** @var $entity Varien_Object|PHPUnit_Framework_MockObject_MockObject */
        $entity = $this->getMockBuilder('Varien_Object')
            ->setMethods(array('setPropertyA', 'setPropertyB'))
            ->getMock();

        $entity->expects($this->once())
            ->method('setPropertyA')
            ->with('a');

        $entity->expects($this->once())
            ->method('setPropertyB')
            ->with('b');

        $this->_callServiceProtectedMethod('_setDataUsingMethods',
            array($entity, array('property_a' => 'a', 'property_b' => 'b')));

        $this->assertEmpty($entity->getData());
    }

    /**
     * Call protected method of service
     *
     * @param string $method
     * @param array $arguments
     * @return
     */
    protected function _callServiceProtectedMethod($method, array $arguments = array())
    {
        $method = new ReflectionMethod($this->_service, $method);
        $method->setAccessible(true);
        return $method->invokeArgs($this->_service, $arguments);
    }
}
