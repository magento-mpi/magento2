<?php
/**
 * File contains tests for Auto Discovery functionality.
 *
 * @copyright {}
 */

/**#@+
 * API resources must be available without auto loader as the file name cannot be calculated from class name.
 */
include_once __DIR__ . '/../_files/data_types/Customer/AddressData.php';
include_once __DIR__ . '/../_files/data_types/CustomerData.php';
include_once __DIR__ . '/../_files/autodiscovery/subresource_class_fixture.php';
/**#@-*/

/**
 * Class implements tests for Mage_Webapi_Helper_Data class.
 */
class Mage_Webapi_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Data */
    protected static $_helper;

    /** @var Mage_Webapi_Model_ConfigAbstract */
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

    /**
     * @return Mage_Webapi_Model_ConfigAbstract
     */
    protected function _getModel()
    {
        if (!self::$_apiConfig) {
            $pathToResourceFixtures = __DIR__ . '/../../_files/autodiscovery';
            self::$_apiConfig = $this->_createResourceConfig($pathToResourceFixtures);
        }
        return self::$_apiConfig;
    }

    public static function tearDownAfterClass()
    {
        self::$_apiConfig = null;
        parent::tearDownAfterClass();
    }

    /**
     * @dataProvider dataProviderForTestPrepareMethodParamsPositive
     * @param string|object $class
     * @param string $methodName
     * @param array $requestData
     * @param array $expectedResult
     */
    public function testPrepareMethodParamsPositive(
        $class,
        $methodName,
        $requestData,
        $expectedResult = array()
    ) {
        $this->markTestSkipped('Skipped until MAGETWO-5507 implemented.');
        $actualResult = self::$_helper->prepareMethodParams($class, $methodName, $requestData, $this->_getModel());
        $this->assertEquals($expectedResult, $actualResult, "The array of arguments was prepared incorrectly.");
    }

    public static function dataProviderForTestPrepareMethodParamsPositive()
    {
        $customerDataObject = new Vendor_Module_Model_Webapi_CustomerData();
        $customerDataObject->email = "test_email@example.com";
        $customerDataObject->firstname = "firstName";
        $customerDataObject->address = new Vendor_Module_Model_Webapi_Customer_AddressData();
        $customerDataObject->address->city = "cityName";
        $customerDataObject->address->street = "streetName";

        /** Test passing of complex type parameter with optional field not set */
        $customerDataWithoutOptionalFieldsInput = array(
            'email' => "test_email@example.com",
            'firstname' => 'firstName'
        );
        $customerDataWithoutOptionalFieldsOutput = new Vendor_Module_Model_Webapi_CustomerData();
        $customerDataWithoutOptionalFieldsOutput->email = "test_email@example.com";
        $customerDataWithoutOptionalFieldsOutput->firstname = "firstName";
        $customerDataWithoutOptionalFieldsOutput->lastname = "DefaultLastName";
        $customerDataWithoutOptionalFieldsOutput->password = "123123q";

        return array(
            // Test valid data that does not need transformations
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
            ),
            // Test filtering unnecessary data
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV2',
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test parameters sorting
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param4' => 4, 'param2' => 2, 'param3' => array($customerDataObject), 'param1' => 1),
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
            ),
            // Test default values setting
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param1' => 1, 'param2' => 2),
                array('param1' => 1, 'param2' => 2, 'param3' => array(), 'param4' => 'default_value'),
            ),
            // Test with object instead of class name
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'createV2',
                array('param2' => 2, 'param1' => 1),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test passing of partially formatted objects
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'updateV1',
                array('param1' => 1, 'param2' => get_object_vars($customerDataObject)),
                array('param1' => 1, 'param2' => $customerDataObject),
            ),
            // Test passing of complex type parameter with optional field not set
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'updateV1',
                array('param1' => 1, 'param2' => $customerDataWithoutOptionalFieldsInput),
                array('param1' => 1, 'param2' => $customerDataWithoutOptionalFieldsOutput),
            ),
        );
    }

    /**
     * Test prepareMethodParams method with unexpected data instead of array.
     */
    public function testPrepareMethodParamsArrayExpectedException()
    {
        $this->markTestSkipped('Skipped until MAGETWO-5507 implemented.');
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Data corresponding to "%s" type is expected to be an array.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        self::$_helper->prepareMethodParams(
            'Vendor_Module_Controller_Webapi_Resource_Subresource',
            'createV1',
            array('param1' => 1, 'param2' => 2, 'param3' => 'not_array', 'param4' => 4),
            $this->_getModel()
        );
    }

    /**
     * Test prepareMethodParams method with complex type equal to unexpected data instead of array.
     */
    public function testPrepareMethodParamsComplexTypeArrayExpectedException()
    {
        $this->markTestSkipped('Skipped until MAGETWO-5507 implemented.');
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Data corresponding to "%s" type is expected to be an array.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        self::$_helper->prepareMethodParams(
            'Vendor_Module_Controller_Webapi_Resource_Subresource',
            'updateV1',
            array('param1' => 1, 'param2' => 'Non array complex data'),
            $this->_getModel()
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
    public function testPrepareMethodParamsNegative(
        $class,
        $methodName,
        $requestData,
        $exceptionClass,
        $exceptionMessage
    ) {
        $this->markTestSkipped('Skipped until MAGETWO-5507 implemented.');
        $this->setExpectedException($exceptionClass, $exceptionMessage);
        self::$_helper->prepareMethodParams($class, $methodName, $requestData, $this->_getModel());
    }

    public static function dataProviderForTestPrepareMethodParamsNegative()
    {
        $customerDataWithoutRequiredField = array(
            'email' => "test_email@example.com"
        );
        return array(
            // Test exception in case of missing required parameter
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param2' => 2, 'param4' => 4),
                'Mage_Webapi_Exception',
                'Required parameter "%s" is missing.'
            ),
            // Test passing of complex type parameter with not specified required field
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
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
        $this->assertEquals(
            $expectedPlural,
            self::$_helper->convertSingularToPlural($singular),
            "Conversion from singular to plural was performed incorrectly."
        );
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

    /**
     * Create resource config initialized with classes found in the specified directory.
     *
     * @return Mage_Webapi_Model_ConfigAbstract
     */
    protected function _createResourceConfig()
    {
        /** Prepare arguments for SUT constructor. */
        $directoryWithFixturesForConfig = __DIR__ . '/../_files/autodiscovery';
        $objectManager = new Magento_ObjectManager_Zend();
        $appConfig = new Mage_Core_Model_Config($objectManager);
        $appConfig->setOptions(array('base_dir' => realpath(__DIR__ . "../../../../../../../../")));
        /** Prepare mocks for SUT constructor. */
        $helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        $helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper');
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));
        $routeFactory = new Magento_Controller_Router_Route_Factory($objectManager);

        /** Initialize SUT. */
        $apiConfig = new Mage_Webapi_Model_Config(
            $helperFactory,
            $appConfig,
            $this->getMockBuilder('Mage_Core_Model_Cache')->disableOriginalConstructor()->getMock(),
            $routeFactory
        );
        $apiConfig->setDirectoryScanner(new Zend\Code\Scanner\DirectoryScanner($directoryWithFixturesForConfig));
        $apiConfig->init();
        return $apiConfig;
    }
}
