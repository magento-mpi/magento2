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

class Mage_Api2_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Api2_Model_Config_Resource
     */
    protected static $_model = null;

    public static function setUpBeforeClass()
    {
        // correct config
        self::$_model = new Mage_Api2_Model_Config_Resource(glob(__DIR__ . '/_files/positive/*/resource.xml'));
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
    }

    public function testGetResourceMethodData()
    {
        $data = self::$_model->getResourceMethodData('customer', 'create');
        $expected = array(
            'description' => 'Create customer',
            'input' => array(
                'customerData' => array(
                    'type' => 'customerEntityCreate',
                    'required' => 1,
                    'maxOccurs' => 1,
                )
            ),
            'output' => array(
                'result' => array(
                    'type' => 'int',
                    'required' => 1,
                    'maxOccurs' => 1,
                )
            )
        );
        $this->assertEquals($expected, $data);
    }

    public function testGetDataType()
    {
        $data = self::$_model->getDataType('productEntity');
        $expected = array(
            'sku' => array(
                'type' => 'string',
                'required' => false,
                'maxOccurs' => '1'
            ),
            'name' => array(
                'type' => 'string',
                'required' => false,
                'maxOccurs' => '1'
            ),
            'price' => array(
                'type' => 'float',
                'required' => false,
                'maxOccurs' => '1'
            ),
            'description' => array(
                'type' => 'string',
                'required' => false,
                'maxOccurs' => '1'
            ),
            'created_at' => array(
                'type' => 'string',
                'required' => false,
                'maxOccurs' => '1'
            )
        );
        $this->assertEquals($expected, $data);
    }

    /**
     * Exception should be thrown if resource operation described into wrong port type (resource)
     *
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Operation "catalogProductCreate" is not related to resource "product".
     */
    public function testInvalideOperationName()
    {
        new Mage_Api2_Model_Config_Resource(
            array(__DIR__ . '/_files/negative/resource_with_incorrect_operation_name.xml'));
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage There is no element "customerCreateRequestParam" with parameters not found.
     */
    public function testInvalideMissingParams()
    {
        new Mage_Api2_Model_Config_Resource(
            array(__DIR__ . '/_files/negative/resource_missing_element_with_parameters.xml'));
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage There is no proper element with parameters for message "customerCreateRequest".
     */
    public function testInvalideMissingParams2()
    {
        new Mage_Api2_Model_Config_Resource(
            array(__DIR__ . '/_files/negative/resource_missing_proper_message_node.xml'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Resource "invalideResource" not found in config.
     */
    public function testGetResourceMethodDataInvalideResource()
    {
        self::$_model->getResourceMethodData('invalideResource', null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Method "invalide" for resource "customer" not found in config.
     */
    public function testGetResourceMethodDataInvalideMethod()
    {
        self::$_model->getResourceMethodData('customer', 'invalide');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Data type "invalideTypeName" not found in config.
     */
    public function testGetDataTypeInvalideName()
    {
        self::$_model->getDataType('invalideTypeName');
    }
}
