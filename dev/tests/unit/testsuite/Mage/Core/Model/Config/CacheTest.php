<?php
/**
 * Test class for Mage_Core_Model_Config_Cache
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Cache
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_containerFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configSectionsMock;

    protected function setUp()
    {
        $this->_cacheMock = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false, false);
        $this->_configSectionsMock = $this->getMock('Mage_Core_Model_Config_Sections',
            array(), array(), '', false, false);
        $this->_containerFactoryMock = $this->getMock('Mage_Core_Model_Config_ContainerFactory',
            array(), array(), '', false, false);
        $this->_baseFactoryMock = $this->getMock('Mage_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_model = new Mage_Core_Model_Config_Cache(
            $this->_cacheMock,
            $this->_configSectionsMock,
            $this->_containerFactoryMock,
            $this->_baseFactoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_cacheMock);
        unset($this->_configSectionsMock);
        unset($this->_containerFactoryMock);
        unset($this->_baseFactoryMock);
        unset($this->_model);
    }


    public function testCacheLifetime()
    {
        $lifetime = 10;
        $this->_model->setCacheLifetime($lifetime);
        $this->assertEquals($lifetime, $this->_model->getCacheLifeTime());
    }

    public function testLoadWithoutConfig()
    {
        $this->assertFalse($this->_model->load());
    }

    public function testLoadWithConfig()
    {
        $this->_cacheMock->expects($this->once())
            ->method('canUse')
            ->with('config')
            ->will($this->returnValue(true));
        $this->_cacheMock->expects($this->at(1))
            ->method('load')
            ->will($this->returnValue(false));
        $this->_cacheMock->expects($this->at(2))
            ->method('load')
            ->will($this->returnValue('test_config'));
        $this->_containerFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array('sourceData' => 'test_config')))
            ->will($this->returnValue('some_instance'));

        $this->assertEquals('some_instance', $this->_model->load());
    }
}
