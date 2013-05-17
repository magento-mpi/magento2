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

    /** @var Mage_Core_Model_Config */
    protected $_config;

    public function setup()
    {
        $this->_config = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $updatesRootPath
            = Mage_Core_Model_Dataservice_Config::CONFIG_AREA . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE;
        $sourcesRoot = new Mage_Core_Model_Config_Element(
            '<config><first><file>' . self::NAMEPART . '/config.xml</file></first></config>');
        $this->_config->expects($this->once())->method('getNode')->with($this->equalTo($updatesRootPath))->will(
            $this->returnValue($sourcesRoot)
        );
        $this->_config->expects($this->once())->method('getModuleDir')->with(
            $this->equalTo('etc'), $this->equalTo(self::NAMEPART)
        )->will($this->returnValue(__DIR__ . '/_files/'));
        $this->_dataserviceConfig = new Mage_Core_Model_Dataservice_Config($this->_config);
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
}