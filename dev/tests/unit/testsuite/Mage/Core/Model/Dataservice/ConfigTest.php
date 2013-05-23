<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_ConfigTest extends PHPUnit_Framework_TestCase
{
    const NAMEPART = 'NAMEPART';

    /** @var Mage_Core_Model_Dataservice_Config */
    protected $_dataserviceConfig;

    /** @var Mage_Core_Model_Config_Base */
    protected $_config;

    /** @var Mage_Core_Model_Config_Loader_Modules_File */
    protected $_fileReader;

    public function setup() {
        $this->_config = $this->getMockBuilder('Mage_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $updatesRootPath
            = Mage_Core_Model_Dataservice_Config::CONFIG_AREA . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE;
        $sourcesRoot = new Mage_Core_Model_Config_Element(
            '<config><first><file>' . self::NAMEPART . '/config.xml</file></first></config>');
        $this->_config->expects($this->once())->method('getNode')->with($this->equalTo($updatesRootPath))->will(
            $this->returnValue($sourcesRoot)
        );
        $this->_fileReader = $this->getMockBuilder('Mage_Core_Model_Config_Loader_Modules_File')->disableOriginalConstructor()->getMock();
        $this->_fileReader->expects($this->once())->method('getModuleDir')->with(
            $this->equalTo('etc'), $this->equalTo(self::NAMEPART)
        )->will($this->returnValue(__DIR__ . '/_files/'));
        $this->_dataserviceConfig = new Mage_Core_Model_Dataservice_Config($this->_config, $this->_fileReader);
    }

    public function testGetClassByAlias() {
        // result should match the config.xml file
        $result = $this->_dataserviceConfig->getClassByAlias('alias');
        $this->assertNotNull($result);
        $this->assertEquals('some_class_name', $result['class']);
        $this->assertEquals('some_method_name', $result['retrieveMethod']);
        $this->assertEquals('foo', $result['methodArguments']['some_arg_name']);
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Service call with name
     */
    public function testGetClassByAliasNotFound() {
        $this->_dataserviceConfig->getClassByAlias('none');
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasInvalidCall() {
        $this->_dataserviceConfig->getClassByAlias('missing_service');
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage must specify file
     */
    public function testInitNoFileElement() {
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $updatesRootPath
            = Mage_Core_Model_Dataservice_Config::CONFIG_AREA . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE;
        $sourcesRoot = new Mage_Core_Model_Config_Element(
            '<config><first></first></config>');
        $configMock->expects($this->once())->method('getNode')->with($this->equalTo($updatesRootPath))->will(
            $this->returnValue($sourcesRoot)
        );
        $this->_dataserviceConfig = new Mage_Core_Model_Dataservice_Config($configMock, $this->_fileReader);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Module is missing in Service calls configuration
     */
    public function testInitNoFilePath() {
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $updatesRootPath
            = Mage_Core_Model_Dataservice_Config::CONFIG_AREA . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE;
        $sourcesRoot = new Mage_Core_Model_Config_Element(
            '<config><first><file>incomplete_path.given</file></first></config>');
        $configMock->expects($this->once())->method('getNode')->with($this->equalTo($updatesRootPath))->will(
            $this->returnValue($sourcesRoot)
        );
        $this->_dataserviceConfig = new Mage_Core_Model_Dataservice_Config($configMock, $this->_fileReader);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage doesn't exist or isn't readable
     */
    public function testInitNoFileFound() {
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $updatesRootPath
            = Mage_Core_Model_Dataservice_Config::CONFIG_AREA . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE;
        $sourcesRoot = new Mage_Core_Model_Config_Element(
            '<config><first><file>' . self::NAMEPART . '/nothing.here</file></first></config>');
        $configMock->expects($this->once())->method('getNode')->with($this->equalTo($updatesRootPath))->will(
            $this->returnValue($sourcesRoot)
        );
        $fileReader = $this->getMockBuilder('Mage_Core_Model_Config_Loader_Modules_File')->disableOriginalConstructor()->getMock();
        $fileReader->expects($this->once())->method('getModuleDir')->with(
            $this->equalTo('etc'), $this->equalTo(self::NAMEPART)
        )->will($this->returnValue(__DIR__ . '/_files/'));
        $this->_dataserviceConfig = new Mage_Core_Model_Dataservice_Config($configMock, $fileReader);
    }
}
