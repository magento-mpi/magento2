<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Config_Modules_Reader
 */
class Magento_Core_Model_Config_Modules_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileReaderMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config_Modules', array(), array(), '', false, false);
        $this->_fileReaderMock = $this->getMock('Magento_Core_Model_Config_Loader_Modules_File',
            array(), array(), '', false, false);
        $this->_model = new Magento_Core_Model_Config_Modules_Reader(
            $this->_configMock,
            $this->_fileReaderMock
        );
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_fileReaderMock);
        unset($this->_model);
    }

    public function testLoadModulesConfiguration()
    {
        $fileName = 'acl.xml';
        $mergeToObjectMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $mergeModelMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_fileReaderMock->expects($this->once())
            ->method('loadConfigurationFromFile')
            ->with($this->equalTo($this->_configMock),
                   $this->equalTo($fileName),
                   $this->equalTo($mergeToObjectMock),
                   $this->equalTo($mergeModelMock))
            ->will($this->returnValue('test_data')
        );
        $result = $this->_model->loadModulesConfiguration($fileName, $mergeToObjectMock, $mergeModelMock);
        $this->assertEquals('test_data', $result);
    }

    public function testGetModuleConfigurationFiles()
    {
        $fileName = 'acl.xml';
        $this->_fileReaderMock->expects($this->once())
            ->method('getConfigurationFiles')
            ->with($this->equalTo($this->_configMock),
                   $this->equalTo($fileName))
            ->will($this->returnValue('test_data')
        );
        $result = $this->_model->getModuleConfigurationFiles($fileName);
        $this->assertEquals('test_data', $result);
    }

    public function testGetModuleDir()
    {
        $type = 'some_type';
        $moduleName = 'some_module';
        $this->_fileReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with($this->equalTo($type),
                   $this->equalTo($moduleName))
            ->will($this->returnValue('test_data')
        );
        $result = $this->_model->getModuleDir($type, $moduleName);
        $this->assertEquals('test_data', $result);
    }
}