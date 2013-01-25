<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Loader_Local
 */
class Mage_Core_Model_Config_Loader_LocalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Loader_Local
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_prototypeFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customFile;

    protected function setUp()
    {
        $this->_customConfig = null;
        $this->_customFile = null;
        $this->_dirsMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $this->_prototypeFactoryMock = $this->getMock('Mage_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_baseConfigMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
    }

    protected function createModel()
    {
        return new Mage_Core_Model_Config_Loader_Local(
            $this->_prototypeFactoryMock,
            $this->_dirsMock,
            $this->_customConfig,
            $this->_customFile
        );
    }

    protected function tearDown()
    {
        unset($this->_prototypeFactoryMock);
        unset($this->_dirsMock);
        unset($this->_baseConfigMock);
        unset($this->_model);
    }

    public function testLoadWithoutData()
    {
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::CONFIG))
            ->will($this->returnValue('testdir\etc'));
        $this->_prototypeFactoryMock->expects($this->never())
            ->method('create');
        $this->_baseConfigMock->expects($this->never())
            ->method('loadFile');
        $this->_baseConfigMock->expects($this->never())
            ->method('loadString');
        $this->_baseConfigMock->expects($this->never())
            ->method('extend');
        $this->createModel()->load($this->_baseConfigMock);
    }

    public function testLoadWithLocalConfig()
    {
        $localConfigFile = realpath(__DIR__. '/../_files/testdir/etc/local.xml');
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::CONFIG))
            ->will($this->returnValue(realpath(__DIR__. '/../_files/testdir/etc')));
        $this->_prototypeFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->with('<config/>')
            ->will($this->returnValue($this->_baseConfigMock));
        $this->_baseConfigMock->expects($this->once())
            ->method('loadFile')
            ->with($this->equalTo($localConfigFile))
            ->will($this->returnValue(true));
        $this->_baseConfigMock->expects($this->exactly(1))
            ->method('extend')
            ->with($this->equalTo($this->_baseConfigMock))
            ->will($this->returnValue($this->getMockBuilder('Varien_Simplexml_Config')
            ->disableOriginalConstructor()->getMock())
        );
        $this->createModel()->load($this->_baseConfigMock);
    }

    public function testLoadWithCustomConfig()
    {
        $localConfigFile = realpath(__DIR__. '/../_files/testdir/etc/local.xml');
        $this->_customFile = 'directorytest' . DS . 'testconfig.xml';
        $localConfigExtraFile = realpath(__DIR__. '/../_files/testdir/etc/directorytest/testconfig.xml');
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::CONFIG))
            ->will($this->returnValue(realpath(__DIR__. '/../_files/testdir/etc/')));
        $this->_prototypeFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->with('<config/>')
            ->will($this->returnValue($this->_baseConfigMock));
        $this->_baseConfigMock->expects($this->at(0))
            ->method('loadFile')
            ->with($this->equalTo($localConfigFile))
            ->will($this->returnValue(true));
        $this->_baseConfigMock->expects($this->at(1))
            ->method('loadFile')
            ->with($this->equalTo($localConfigExtraFile))
            ->will($this->returnValue(true));
        $this->_baseConfigMock->expects($this->exactly(2))
            ->method('extend')
            ->with($this->equalTo($this->_baseConfigMock))
            ->will($this->returnValue($this->getMockBuilder('Varien_Simplexml_Config')
                ->disableOriginalConstructor()->getMock())
        );
        $this->createModel()->load($this->_baseConfigMock);
    }

    public function testLoadWithExtraLocalConfig()
    {
        $this->_customConfig = realpath(__DIR__. '/../_files/testdir/etc/testdirectory/customconfig.xml');
        $this->_dirsMock->expects($this->once())
            ->method('getDir')
            ->with($this->equalTo(Mage_Core_Model_Dir::CONFIG))
            ->will($this->returnValue(realpath(__DIR__. '/../_files/testdir/etc/testdirectory')));
        $this->_prototypeFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->with('<config/>')
            ->will($this->returnValue($this->_baseConfigMock));
        $this->_baseConfigMock->expects($this->never())
            ->method('loadFile');
        $this->_baseConfigMock->expects($this->exactly(1))
            ->method('loadString')
            ->with($this->equalTo($this->_customConfig))
            ->will($this->returnValue(true));
        $this->_baseConfigMock->expects($this->exactly(1))
            ->method('extend')
            ->with($this->equalTo($this->_baseConfigMock))
            ->will($this->returnValue($this->getMockBuilder('Varien_Simplexml_Config')
                ->disableOriginalConstructor()->getMock())
        );
        $this->createModel()->load($this->_baseConfigMock);
    }
}