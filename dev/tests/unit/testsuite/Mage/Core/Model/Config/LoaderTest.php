<?php
/**
 * Test class for Mage_Core_Model_Config_Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Loader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_primaryConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderLocalMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    protected function setUp()
    {
        $this->_primaryConfigMock = $this->getMock(
            'Mage_Core_Model_Config_Primary', array(), array(), '', false, false
        );

        $this->_resourceConfigMock = $this->getMock(
            'Mage_Core_Model_Config_Resource', array(), array(), '', false, false
        );

        $this->_modulesReaderMock = $this->getMock(
            'Mage_Core_Model_Config_Modules_Reader', array(), array(), '', false, false
        );

        $this->_loaderLocalMock = $this->getMock(
            'Mage_Core_Model_Config_Loader_Local', array(), array(), '', false, false
        );

        $this->_baseConfigMock = $this->getMock(
            'Mage_Core_Model_Config_Base', array(), array(), '', false, false
        );

        $this->_model = new Mage_Core_Model_Config_Loader(
            $this->_primaryConfigMock,
            $this->_resourceConfigMock,
            $this->_modulesReaderMock,
            $this->_loaderLocalMock
        );
    }

    public function testLoadWithEmptyConfig()
    {
        /** Test load initial xml */
        $this->_baseConfigMock->expects($this->once())->method('getNode')->will($this->returnValue(null));
        $this->_baseConfigMock->expects($this->once())->method('loadString')->with('<config></config>');

        /** Test extends config with primary config values */
        $this->_baseConfigMock->expects($this->once())->method('extend')->with($this->_primaryConfigMock);

        /** Test loading of DB provider specific config files */
        $this->_resourceConfigMock->expects($this->once())
            ->method('getResourceConnectionModel')
            ->with('core')
            ->will($this->returnValue('mysql4'));
        $this->_modulesReaderMock->expects($this->once())
            ->method('loadModulesConfiguration')
            ->with(array('config.xml', 'config.mysql4.xml'), $this->_baseConfigMock);

        /** Test preventing overriding of local configuration */
        $this->_loaderLocalMock->expects($this->once())->method('load')->with($this->_baseConfigMock);

        /** Test merging of all config data */
        $this->_baseConfigMock->expects($this->once())->method('applyExtends');

        $this->_model->load($this->_baseConfigMock);
    }

    /**
     * @depends testLoadWithEmptyConfig
     */
    public function testLoadWithNotEmptyConfig()
    {
        /** Test load initial xml */
        $this->_baseConfigMock->expects($this->once())->method('getNode')->will($this->returnValue('some value'));
        $this->_baseConfigMock->expects($this->never())->method('loadString');

        $this->_model->load($this->_baseConfigMock);
    }
}
