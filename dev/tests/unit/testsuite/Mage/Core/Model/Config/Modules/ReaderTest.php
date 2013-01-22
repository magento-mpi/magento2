<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Modules_Reader
 */
class Mage_Core_Model_Config_Modules_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Modules_Reader
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
        $this->_configMock = $this->getMock('Mage_Core_Model_Config_Modules', array(), array(), '', false, false);
        $this->_fileReaderMock = $this->getMock('Mage_Core_Model_Config_Loader_Modules_File',
            array(), array(), '', false, false);
        $this->_model = new Mage_Core_Model_Config_Modules_Reader(
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

    public function testLoadModulesConfigurationWithData()
    {
        $fileName = 'test';
        $mergeToObjectMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $mergeModelMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_fileReaderMock->expects($this->once())
            ->method('loadConfigurationFromFile')
            ->with($this->equalTo($this->_configMock),
                   $this->equalTo($fileName),
                   $this->equalTo($mergeToObjectMock),
                   $this->equalTo($mergeModelMock)
        );
        $this->_model->loadModulesConfiguration($fileName, $mergeToObjectMock, $mergeModelMock);
    }

    public function testLoadModulesConfigurationWithoutData()
    {
        $fileName = null;
        $this->_fileReaderMock->expects($this->once())
            ->method('loadConfigurationFromFile');
        $this->_model->loadModulesConfiguration($fileName);
        }

    public function testGetModuleConfigurationFilesWithData()
    {
        $fileName = 'test';
        $this->_fileReaderMock->expects($this->once())
            ->method('getConfigurationFiles')
            ->with($this->equalTo($this->_configMock),
                   $this->equalTo($fileName)
        );
        $this->_model->getModuleConfigurationFiles($fileName);
    }

    public function testGetModuleConfigurationFilesWithoutData()
    {
        $fileName = null;
        $this->_configMock = null;
        $this->_fileReaderMock->expects($this->once())
            ->method('getConfigurationFiles');
        $this->_model->getModuleConfigurationFiles($fileName);
    }

    public function testGetModuleDirWithData()
    {
        $type = 'some_type';
        $moduleName = 'some_module';
        $this->_fileReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with($this->equalTo($this->_configMock),
                   $this->equalTo($type),
                   $this->equalTo($moduleName)
        );
        $this->_model->getModuleDir($type, $moduleName);
    }

    public function testGetModuleDirWithoutData()
    {
        $type = null;
        $moduleName = null;
        $this->_fileReaderMock->expects($this->once())
            ->method('getModuleDir');
        $this->_model->getModuleDir($type, $moduleName);
    }
}