<?php
/**
 * Test class for Magento_Core_Model_Config_Loader_Locales
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_Loader_LocalesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Loader_Locales
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_dirsMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_factoryMock = $this->getMock(
            'Magento_Core_Model_Config_BaseFactory', array(), array(), '', false, false
        );
        $this->_model = new Magento_Core_Model_Config_Loader_Locales(
            $this->_dirsMock,
            $this->_factoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_dirsMock);
        unset($this->_factoryMock);
        unset($this->_baseConfigMock);
        unset($this->_model);
    }

    public function testLoad()
    {
        $this->_dirsMock->expects(
            $this->once())->method('getDir')->will($this->returnValue( __DIR__ . '/../_files/locale')
        );
        $mergeMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $mergeMock->expects($this->exactly(4))->method('loadFile')->with($this->stringEndsWith('config.xml'));
        $this->_factoryMock->expects($this->exactly(4))->method('create')->will($this->returnValue($mergeMock));
        $this->_model->load($this->_baseConfigMock);
    }

    public function testLoadConditions()
    {
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->will($this->returnValue(__DIR__ . '/_files/locale/etc/etc/'));
        $this->_factoryMock->expects($this->never())->method('create');
        $this->_baseConfigMock->expects($this->never())->method('extend');
        $this->_model->load($this->_baseConfigMock);
    }
}
