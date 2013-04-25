<?php
/**
 * Test class for Mage_Core_Model_Config_Loader_Modules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Loader_ModulesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mage_Core_Model_Config_Loader_Modules
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_primaryConfigMock;

    /**
     * PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceConfigMock;

    /**
     * PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileReaderMock;

    /**
     * PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sortedFactoryMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_primaryConfigMock = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false, false);
        $this->_resourceConfigMock = $this->getMock('Mage_Core_Model_Config_Resource', array(), array(), '', false, false);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_fileReaderMock = $this->getMock('Mage_Core_Model_Config_Loader_Modules_File', array(), array(), '', false);
        $this->_sortedFactoryMock = $this->getMock('Mage_Core_Model_Config_Modules_SortedFactory', array('create'), array(), '', false);
        $arguments = array(
            'primaryConfig' => $this->_primaryConfigMock,
            'resourceConfig' => $this->_resourceConfigMock,
            'objectManager' => $this->_objectManagerMock,
            'dirs' => $this->_dirMock,
            'sortedFactory' => $this->_sortedFactoryMock,
            'fileReader' => $this->_fileReaderMock,
        );
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $helper->getObject('Mage_Core_Model_Config_Loader_Modules', $arguments);

    }

    public function testLoad()
    {
        $nodeMock = $this->getMock('stdClass', array('asArray'), array(), '', false);
        $data = array('someKey' => 'someValue');
        $nodeMock->expects($this->once())->method('asArray')->will($this->returnValue($data));

        $this->_configMock
            ->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue($nodeMock));

        $path = realpath(__DIR__ . '/../_files/modules/');
        $this->_dirMock->expects($this->any())->method("getDir")->will($this->returnValue($path));

        $sortedConfigMock = $this->getMock('Mage_Core_Model_Config_Modules_Sorted', array(), array(), '', false);
        $this->_sortedFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($sortedConfigMock));

        $this->_configMock
            ->expects($this->exactly(3))
            ->method('extend')
            ->with($this->logicalOr($this->equalTo($this->_primaryConfigMock), $this->equalTo($sortedConfigMock)));

        $this->_fileReaderMock
            ->expects($this->once())
            ->method('loadConfigurationFromFile');

        $this->_configMock->expects($this->once())->method('applyExtends');

        $this->_objectManagerMock
            ->expects($this->once())
            ->method('configure')
            ->with($this->equalTo($data));

        $this->_model->load($this->_configMock);
    }
}

