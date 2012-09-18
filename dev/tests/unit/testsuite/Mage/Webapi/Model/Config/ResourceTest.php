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

class Mage_Webapi_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Config_Resource
     */
    protected static $_model = null;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_magentoConfigMock;

    protected function setUp()
    {
        $optionsMock = $this->getMockBuilder('Mage_Core_Model_Config_Options')
            ->setMethods(array('getCodeDir'))
            ->disableOriginalConstructor()
            ->getMock();
        $optionsMock->expects($this->any())
            ->method('getCodeDir')
            ->will($this->returnValue(''));
        $magentoConfigMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->setMethods(array('getOptions'))
            ->disableOriginalConstructor()
            ->getMock();
        $magentoConfigMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($optionsMock));
        $this->_magentoConfigMock = $magentoConfigMock;

        /** @var Mage_Core_Model_Config $magentoConfigMock */
        self::$_model = new Mage_Webapi_Model_Config_Resource(glob(__DIR__ . '/_files/positive/*/resource.xml'),
            $magentoConfigMock);
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

    public function testGetResourceNameByOperation()
    {
        $actualResourceName = self::$_model->getResourceNameByOperation('catalogCategoryTree');
        $expectedResourceName = 'catalogCategory';
        $this->assertEquals($expectedResourceName, $actualResourceName,
            'Resource name was defined incorrectly by given operation.');
    }

    public function testGetResourceNameByOperationInvalidOperation()
    {
        $actualResourceName = self::$_model->getResourceNameByOperation('invalid');
        $this->assertFalse($actualResourceName, "In case of invalid operation 'false' is expected.");
    }

    public function testGetMethodNameByOperation()
    {
        $actualMethodName = self::$_model->getMethodNameByOperation('customerInfo');
        $expectedMethodName = 'info';
        $this->assertEquals($expectedMethodName, $actualMethodName,
            'Method name was identified incorrectly by given operation');
    }

    public function testGetMethodNameByOperationInvalidOperation()
    {
        $actualMethodName = self::$_model->getMethodNameByOperation('invalid');
        $this->assertFalse($actualMethodName, "In case of invalid operation 'false' is expected.");
    }

    public function testGetDom()
    {
        $domDocument = self::$_model->getDom();
        $this->assertInstanceOf('DOMDocument', $domDocument);
        $xml = $domDocument->saveXML();
        $this->assertValidXml($xml);
    }

    public function testGetResources()
    {
        $actualResources = self::$_model->getResources();
        $expectedResourceNames = array('customer', 'product', 'catalogCategory', 'catalogProductAttribute');
        // check the first level keys only
        $actualResourceNames = array_keys($actualResources);
        $this->assertSame($expectedResourceNames, $actualResourceNames);
    }

    /**
     * Exception should be thrown if resource operation described into wrong port type (resource)
     *
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Operation "catalogProductCreate" is not related to resource "product".
     */
    public function testInvalidOperationName()
    {
        new Mage_Webapi_Model_Config_Resource(
            array(__DIR__ . '/_files/negative/resource_with_incorrect_operation_name.xml'), $this->_magentoConfigMock);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage There is no element "customerCreateRequestParam" with parameters.
     */
    public function testInvalidMissingParams()
    {
        new Mage_Webapi_Model_Config_Resource(
            array(__DIR__ . '/_files/negative/resource_missing_element_with_parameters.xml'),
            $this->_magentoConfigMock);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage There is no proper element with parameters for message "customerCreateRequest".
     */
    public function testInvalidMissingParams2()
    {
        new Mage_Webapi_Model_Config_Resource(
            array(__DIR__ . '/_files/negative/resource_missing_proper_message_node.xml'), $this->_magentoConfigMock);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Resource "invalidResource" is not found in config.
     */
    public function testGetResourceMethodDataInvalidResource()
    {
        self::$_model->getResourceMethodData('invalidResource', null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Method "invalid" for resource "customer" is not found in config.
     */
    public function testGetResourceMethodDataInvalidMethod()
    {
        self::$_model->getResourceMethodData('customer', 'invalid');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Data type "invalidTypeName" is not found in config.
     */
    public function testGetDataTypeInvalidName()
    {
        self::$_model->getDataType('invalidTypeName');
    }

    /**
     * Custom assertion for XML document validation
     *
     * @param string $xmlDocument
     */
    public static function assertValidXml($xmlDocument)
    {
        try {
            simplexml_load_string($xmlDocument);
        } catch (Exception $e) {
            self::fail("Provided document is not valid XML document.");
        }
    }
}
