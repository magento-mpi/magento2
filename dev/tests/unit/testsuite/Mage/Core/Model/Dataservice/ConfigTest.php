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

    /** @var  Mage_Core_Model_Config_Modules_Reader */
    private $_reader;

    public function setup()
    {
        $updatesRootPath = Mage_Core_Model_Dataservice_Config::CONFIG_AREA
            . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE;
        $sourcesRoot = new Mage_Core_Model_Config_Element(
            '<config><first><file>' . self::NAMEPART . '/config.xml</file></first></config>'
        );
        $this->_reader = $this->getMockBuilder('Mage_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_reader->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->with('service_calls.xml')
            ->will($this->returnValue(array(__DIR__ . '/_files/service_calls.xml')));

        $this->_dataserviceConfig = new Mage_Core_Model_Dataservice_Config($this->_reader);
    }

    public function testGetClassByAlias()
    {
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
    public function testGetClassByAliasNotFound()
    {
        $this->_dataserviceConfig->getClassByAlias('none');
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasInvalidCall()
    {
        $this->_dataserviceConfig->getClassByAlias('missing_service');
    }

}
