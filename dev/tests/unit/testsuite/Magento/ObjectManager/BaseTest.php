<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager_Base
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_configMock->expects($this->any())
            ->method('getModelClassName')
            ->will($this->returnArgument(0));
        $this->_model = new Magento_ObjectManager_Base($this->_configMock);
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateClassWithoutFactoryThrowsException()
    {
        $this->_model->create('Magento_DummyObject');
    }
}
