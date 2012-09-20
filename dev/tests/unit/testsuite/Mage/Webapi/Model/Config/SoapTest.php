<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Config_SoapTest extends PHPUnit_Framework_TestCase
{
    public function testGetSchemaFile()
    {
        $config = new Mage_Webapi_Model_Config_Soap(array(__DIR__ . '/_files/positive/module_a/soap.xml'));
        $this->assertFileExists($config->getSchemaFile());
    }

    public function testGetControllerClassByResourceName()
    {
        $config = new Mage_Webapi_Model_Config_Soap(array(__DIR__ . '/_files/positive/module_a/soap.xml'));
        $controller = $config->getControllerClassByResourceName('test_module_a');
        $this->assertEquals('Mage_Test_Module_Api_Controller', $controller);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetControllerClassByResourceNameInvalidNameException()
    {
        $config = new Mage_Webapi_Model_Config_Soap(array(__DIR__ . '/_files/positive/module_a/soap.xml'));
        $config->getControllerClassByResourceName('invalid_resource_name');
    }

    /**
     * Exception should be thrown if there are no controller defined for resource
     *
     * @expectedException Magento_Exception
     */
    public function testEmptyController()
    {
        new Mage_Webapi_Model_Config_Soap(array(__DIR__ . '/_files/negative/empty_controller.xml'));
    }
}
