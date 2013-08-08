<?php
/**
 * Test class for Magento_Core_Model_Config_Loader_Modules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_Loader_ModulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Loader_Modules
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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sortedFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localLoaderMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_primaryConfigMock =
            $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false, false);
        $this->_resourceConfigMock =
            $this->getMock('Magento_Core_Model_Config_Resource', array(), array(), '', false, false);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_dirMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $this->_fileReaderMock =
            $this->getMock('Magento_Core_Model_Config_Loader_Modules_File', array(), array(), '', false);
        $this->_sortedFactoryMock =
            $this->getMock('Magento_Core_Model_Config_Modules_SortedFactory', array('create'), array(), '', false);
        $this->_localLoaderMock = $this->getMock('Magento_Core_Model_Config_Loader_Local', array(), array(), '', false);
        $arguments = array(
            'primaryConfig' => $this->_primaryConfigMock,
            'resourceConfig' => $this->_resourceConfigMock,
            'objectManager' => $this->_objectManagerMock,
            'dirs' => $this->_dirMock,
            'sortedFactory' => $this->_sortedFactoryMock,
            'fileReader' => $this->_fileReaderMock,
            'localLoader' => $this->_localLoaderMock
        );
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $helper->getObject('Magento_Core_Model_Config_Loader_Modules', $arguments);
    }

    public function testLoad()
    {
        $path = realpath(__DIR__ . '/../_files/modules/');
        $this->_dirMock->expects($this->any())->method("getDir")->will($this->returnValue($path));

        $sortedConfigMock = $this->getMock('Magento_Core_Model_Config_Modules_Sorted', array(), array(), '', false);
        $this->_sortedFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($sortedConfigMock));

        $this->_configMock
            ->expects($this->exactly(2))
            ->method('extend')
            ->with($this->logicalOr($this->equalTo($this->_primaryConfigMock), $this->equalTo($sortedConfigMock)));

        $this->_localLoaderMock->expects($this->once())->method('load')->with($this->_configMock);

        $this->_fileReaderMock->expects($this->once())->method('loadConfigurationFromFile');

        $this->_configMock->expects($this->once())->method('applyExtends');

        $this->_model->load($this->_configMock);
    }
}
