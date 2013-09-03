<?php
/**
 * Test class for Magento_Core_Model_Config_Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Loader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localesConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbLoaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseFactoryMock;

    protected function setUp()
    {
        $this->_modulesConfigMock = $this->getMock('Magento_Core_Model_Config_Modules',
            array('getNode'), array(), '', false, false);
        $this->_localesConfigMock = $this->getMock('Magento_Core_Model_Config_Locales',
            array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Magento_Core_Model_Config_Base',
            array('extend'), array(), '', false, false);
        $this->_dbLoaderMock = $this->getMock(
            'Magento_Core_Model_Config_Loader_Db', array(), array(), '', false, false
        );
        $this->_baseFactoryMock = $this->getMock('Magento_Core_Model_Config_BaseFactory',
            array('create'), array(), '', false, false);
        $this->_model = new Magento_Core_Model_Config_Loader(
            $this->_modulesConfigMock,
            $this->_localesConfigMock,
            $this->_dbLoaderMock,
            $this->_baseFactoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_modulesConfigMock);
        unset($this->_localesConfigMock);
        unset($this->_dbLoaderMock);
        unset($this->_baseConfigMock);
        unset($this->_baseFactoryMock);
        unset($this->_model);
    }

    public function testLoad()
    {
        $element = new \Magento\Simplexml\Element('<config>test_data</config>');
        $elementConfig = new \Magento\Simplexml\Config();
        $this->_modulesConfigMock->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue($element));
        $this->_localesConfigMock->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue($element));
        $this->_baseFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->with($element)
            ->will($this->returnValue($elementConfig));
        $this->_baseConfigMock->expects($this->exactly(2))
            ->method('extend')
            ->with($this->equalTo($elementConfig))
            ->will($this->returnValue($element));
        $this->_model->load($this->_baseConfigMock);
    }
}
