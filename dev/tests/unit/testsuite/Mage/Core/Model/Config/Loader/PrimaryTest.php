<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Loader_Primary
 */
class Mage_Core_Model_Config_Loader_PrimaryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Loader_Primary
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_protFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localLoaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_simpleXmlConfig;

    protected function setUp()
    {
        $this->_simpleXmlConfig = $this->getMock('Varien_Simplexml_Config',
            array('getNode'), array(), '', false, false);
        $this->_protFactoryMock = $this->getMock('Mage_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_dirsMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $this->_localLoaderMock = $this->getMock('Mage_Core_Model_Config_Loader_Local',
            array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_model = new Mage_Core_Model_Config_Loader_Primary(
            $this->_protFactoryMock,
            $this->_dirsMock,
            $this->_localLoaderMock
        );
    }

    protected function tearDown()
    {
        unset($this->_protFactoryMock);
        unset($this->_dirsMock);
        unset($this->_localLoaderMock);
        unset($this->_baseConfigMock);
        unset($this->_model);
    }

    public function testLoadWithData()
    {
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::CONFIG))
            ->will($this->returnValue(realpath(__DIR__. '/../_files/testdir/etc')));
        $this->_baseConfigMock->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue($this->getMockBuilder('Varien_Simplexml_Config')));
        $this->_protFactoryMock->expects($this->once())
            ->method('create')
            ->with('<config/>')
            ->will($this->returnValue($this->_baseConfigMock));
        $this->_baseConfigMock->expects($this->once())
            ->method('loadFile')
            ->with($this->equalTo(realpath(__DIR__. '/../_files/testdir/etc/testconfig.xml')))
            ->will($this->returnValue(true));
        $this->_baseConfigMock->expects($this->once())
            ->method('extend')
            ->with($this->equalTo($this->_baseConfigMock))
            ->will($this->returnValue($this->_simpleXmlConfig));
        $this->_localLoaderMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($this->_baseConfigMock));
        $this->_model->load($this->_baseConfigMock);
    }

    public function testLoadWithoutData()
    {
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::CONFIG))
            ->will($this->returnValue(realpath(__DIR__. '/../_files/dirtest/etc')));
        $this->_baseConfigMock->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue($this->getMockBuilder('Varien_Simplexml_Config')));
        $this->_protFactoryMock->expects($this->never())
            ->method('create');
        $this->_baseConfigMock->expects($this->never())
            ->method('loadFile');
        $this->_baseConfigMock->expects($this->never())
            ->method('extend');
        $this->_localLoaderMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($this->_baseConfigMock));
        $this->_model->load($this->_baseConfigMock);
    }
}