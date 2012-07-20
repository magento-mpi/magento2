<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Api2_Model_Config_SoapTest extends PHPUnit_Framework_TestCase
{
    public function testGetSchemaFile()
    {
        $config = new Mage_Api2_Model_Config_Soap(array(__DIR__ . '/_files/positive/module_a/soap.xml'));
        $this->assertFileExists($config->getSchemaFile());
    }

    public function testGetControllers()
    {
        $config = new Mage_Api2_Model_Config_Soap(array(__DIR__ . '/_files/positive/module_a/soap.xml'));
        $controllers = $config->getControllers();
        $this->assertInternalType('array', $controllers);

        $expected = array(
            'test_module_a' => 'Mage_Test_Module_Api_Controller',
            'test_module_b' => 'Mage_Test_Moduleb_Api_Controller',
        );
        $this->assertEquals($expected, $controllers);
    }

    /**
     * Exception should be thrown if there are no controller defined for resource
     *
     * @expectedException Magento_Exception
     */
    public function testEmptyController()
    {
        new Mage_Api2_Model_Config_Soap(array(__DIR__ . '/_files/negative/empty_controller.xml'));
    }
}