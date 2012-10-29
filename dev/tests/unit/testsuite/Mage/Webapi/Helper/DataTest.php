<?php
/**
 * File contains tests for Auto Discovery functionality.
 *
 * @copyright {}
 */

include_once __DIR__ . '/../_files/Customer/Address/DataStructure.php';
include_once __DIR__ . '/../_files/Customer/DataStructure.php';
include_once __DIR__ . '/../_files/subresource_class_fixture.php';

/**
 * Class implements tests for Mage_Webapi_Helper_Data class.
 */
class Mage_Webapi_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Data */
    protected static $_helper;

    /** @var Mage_Webapi_Model_Config_Resource */
    protected static $_apiConfig;

    protected function setUp()
    {
        self::$_helper = self::getMock('Mage_Webapi_Helper_Data', array('__'));
        self::$_helper->expects(self::any())->method('__')->will(self::returnArgument(0));
        parent::setUp();
    }

    protected function tearDown()
    {
        self::$_helper = null;
        parent::tearDown();
    }

    public static function setUpBeforeClass()
    {
        $directoryWithFixturesForConfig = __DIR__ . '/../_files';
        $appConfig = new Mage_Core_Model_Config();
        $appConfig->setOptions(array('base_dir' => realpath(__DIR__ . "../../../../../../../../")));
        self::$_apiConfig = new Mage_Webapi_Model_Config_Resource(array(
            'directoryScanner' => new \Zend\Code\Scanner\DirectoryScanner($directoryWithFixturesForConfig),
            'applicationConfig' => $appConfig,
            'autoloader' => Magento_Autoload::getInstance()->addFilesMap(array(
                'Vendor_Module_Webapi_Customer_Address_DataStructure' =>
                    $directoryWithFixturesForConfig . 'Customer/Address/DataStructure.php',
                'Vendor_Module_Webapi_Customer_DataStructure' =>
                    $directoryWithFixturesForConfig . 'Customer/DataStructure.php',
            )),
            'helper' => new Mage_Webapi_Helper_Data()
        ));
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        self::$_apiConfig = null;
        parent::tearDownAfterClass();
    }

    /**
     * @dataProvider dataProviderForTestToArray
     * @param $objectToBeConverted
     * @param $expectedResult
     */
    public function testToArray($objectToBeConverted, $expectedResult)
    {
        self::$_helper->toArray($objectToBeConverted);
        $this->assertSame($expectedResult, $objectToBeConverted, "Object to array conversion failed.");
    }

    public static function dataProviderForTestToArray()
    {
        return array(
            // Test case without need in conversion
            array(
                array('key1' => 1, 'key2' => 'value2', 'key3' => array(3)),
                array('key1' => 1, 'key2' => 'value2', 'key3' => array(3))
            ),
            // Test case with indexed array
            array(
                (object)array('key1' => 1, 'key2' => 'value2', 'key3' => array(3, 'value3')),
                array('key1' => 1, 'key2' => 'value2', 'key3' => array(3, 'value3'))
            ),
            // Test mixed values in array
            array(
                array('key1' => 1, 'key2' => 'value2', 'key3' => (object)array('key3-1' => 'value3-1', 'key3-2' => 32)),
                array('key1' => 1, 'key2' => 'value2', 'key3' => array('key3-1' => 'value3-1', 'key3-2' => 32))
            ),
            // Test recursive converting capabilities
            array(
                (object)array('key1' => array('key2' => (object)array('key3' => array('key4' => 'value4')))),
                array('key1' => array('key2' => array('key3' => array('key4' => 'value4'))))
            ),
        );
    }

    /**
     * @dataProvider dataProviderForTestPrepareMethodParamsPositive
     * @param string|object $class
     * @param string $methodName
     * @param array $requestData
     * @param array $expectedResult
     */
    public function testPrepareMethodParamsPositive($class, $methodName, $requestData,
        $expectedResult = array()
    ) {
        $actualResult = self::$_helper->prepareMethodParams($class, $methodName, $requestData, self::$_apiConfig);
        $this->assertEquals($expectedResult, $actualResult, "The array of arguments was prepared incorrectly.");
    }

    public static function dataProviderForTestPrepareMethodParamsPositive()
    {
        $customerDataObject = new Vendor_Module_Webapi_Customer_DataStructure();
        $customerDataObject->email = "test_email@example.com";
        $customerDataObject->firstname = "firstName";
        $customerDataObject->address = new Vendor_Module_Webapi_Customer_Address_DataStructure();
        $customerDataObject->address->city = "cityName";
        $customerDataObject->address->street = "streetName";

        /** Test passing of complex type parameter with optional field not set */
        $customerDataWithoutOptionalFieldsInput = array(
            'email' => "test_email@example.com",
            'firstname' => 'firstName'
        );
        $customerDataWithoutOptionalFieldsOutput = new Vendor_Module_Webapi_Customer_DataStructure();
        $customerDataWithoutOptionalFieldsOutput->email = "test_email@example.com";
        $customerDataWithoutOptionalFieldsOutput->firstname = "firstName";
        $customerDataWithoutOptionalFieldsOutput->lastname = "DefaultLastName";
        $customerDataWithoutOptionalFieldsOutput->password = "123123q";

        return array(
            // Test valid data that does not need transformations
            array(
                'Vendor_Module_Webapi_Resource_SubresourceController',
                'createV1',
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
            ),
            // Test filtering unnecessary data
            array(
                'Vendor_Module_Webapi_Resource_SubresourceController',
                'createV2',
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test parameters sorting
            array(
                'Vendor_Module_Webapi_Resource_SubresourceController',
                'createV1',
                array('param4' => 4, 'param2' => 2, 'param3' => array($customerDataObject), 'param1' => 1),
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
            ),
            // Test default values setting
            array(
                'Vendor_Module_Webapi_Resource_SubresourceController',
                'createV1',
                array('param1' => 1, 'param2' => 2),
                array('param1' => 1, 'param2' => 2, 'param3' => array(), 'param4' => 'default_value'),
            ),
            // Test with object instead of class name
            array(
                new Vendor_Module_Webapi_Resource_SubresourceController(),
                'createV2',
                array('param2' => 2, 'param1' => 1),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test passing of partially formatted objects
            array(
                new Vendor_Module_Webapi_Resource_SubresourceController(),
                'updateV1',
                array('param1' => 1, 'param2' => get_object_vars($customerDataObject)),
                array('param1' => 1, 'param2' => $customerDataObject),
            ),
            // Test passing of complex type parameter with optional field not set
            array(
                new Vendor_Module_Webapi_Resource_SubresourceController(),
                'updateV1',
                array('param1' => 1, 'param2' => $customerDataWithoutOptionalFieldsInput),
                array('param1' => 1, 'param2' => $customerDataWithoutOptionalFieldsOutput),
            ),
        );
    }

    /**
     * @dataProvider dataProviderForTestPrepareMethodParamsNegative
     * @param string|object $class
     * @param string $methodName
     * @param array $requestData
     * @param string $exceptionClass
     * @param string $exceptionMessage
     */
    public function testPrepareMethodParamsNegative($class, $methodName, $requestData, $exceptionClass,
        $exceptionMessage) {
        $this->setExpectedException($exceptionClass, $exceptionMessage);
        self::$_helper->prepareMethodParams($class, $methodName, $requestData, self::$_apiConfig);
    }

    public static function dataProviderForTestPrepareMethodParamsNegative()
    {
        $customerDataWithoutRequiredField = array(
            'email' => "test_email@example.com"
        );
        return array(
            // Test exception in case of missing required parameter
            array(
                'Vendor_Module_Webapi_Resource_SubresourceController',
                'createV1',
                array('param2' => 2, 'param4' => 4),
                'Mage_Webapi_Exception',
                'Required parameter "%s" is missing.'
            ),
            // Test passing of complex type parameter with not specified required field
            array(
                new Vendor_Module_Webapi_Resource_SubresourceController(),
                'updateV1',
                array('param1' => 1, 'param2' => $customerDataWithoutRequiredField),
                'Mage_Webapi_Exception',
                'Value of "%s" attribute is required.'
            ),
        );
    }

    /**
     * @dataProvider dataProviderForTestConvertSingularToPlural
     */
    public function testConvertSingularToPlural($singular, $expectedPlural)
    {
        $this->assertEquals($expectedPlural, self::$_helper->convertSingularToPlural($singular),
            "Conversion from singular to plural was performed incorrectly.");
    }

    public static function dataProviderForTestConvertSingularToPlural()
    {
        return array(
            array('customer', 'customers'),
            array('category', 'categories'),
            array('webapi', 'webapis'),
            array('downloadable', 'downloadables'),
            array('eway', 'eways'),
            array('tax', 'taxes'),
            array('', '')
        );
    }
}

