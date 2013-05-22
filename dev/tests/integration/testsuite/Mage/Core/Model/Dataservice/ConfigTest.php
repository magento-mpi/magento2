<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Dataservice_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_mockConfig;

    public function setup() {
        $xml
            = <<<XML
<?xml version="1.0"?>
    <global>
        <service_calls>
                <file>_files/service_calls.xml</file>
        </service_calls>
    </global>
XML;
        $configElement = new Mage_Core_Model_Config_Element($xml);
        $this->_mockConfig = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $this->_mockConfig->expects($this->once())->method('getNode')->with(
            $this->equalTo(
                Mage_Core_Model_Dataservice_Config::CONFIG_AREA . '/' . Mage_Core_Model_Dataservice_Config::CONFIG_NODE
            )
        )->will($this->returnValue($configElement));
        $this->_mockConfig->expects($this->once())->method('getModuleDir')->with(
            $this->equalTo('etc'), $this->equalTo('_files')
        )->will($this->returnValue(__DIR__ . '/_files'));
        $this->_config = new Mage_Core_Model_Dataservice_Config($this->_mockConfig);
    }

    public function testGetClassByAlias() {
        $classInfo = $this->_config->getClassByAlias('selectedProductDetails');
        $this->assertEquals('Class_Name', $classInfo['class']);
        $this->assertEquals('retrieve_method', $classInfo['retrieveMethod']);
        $this->assertEquals(array('argument_one' => '{{value_one}}'), $classInfo['methodArguments']);
    }
}