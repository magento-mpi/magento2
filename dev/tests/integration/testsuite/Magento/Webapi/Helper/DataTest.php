<?php
/**
 * File contains tests for Auto Discovery functionality.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**#@+
 * API resources must be available without auto loader as the file name cannot be calculated from class name.
 */
include_once __DIR__ . '/../_files/data_types/Customer/AddressData.php';
include_once __DIR__ . '/../_files/data_types/CustomerData.php';
include_once __DIR__ . '/../_files/autodiscovery/resource_class_fixture.php';
include_once __DIR__ . '/../_files/autodiscovery/subresource_class_fixture.php';
/**#@-*/

/**
 * Class implements tests for \Magento\Webapi\Helper\Data class.
 */
class Magento_Webapi_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Helper\Data */
    protected $_helper;

    /** @var \Magento\Webapi\Model\ConfigAbstract */
    protected $_apiConfig;

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Webapi\Helper\Data');
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** Prepare arguments for SUT constructor. */
        $pathToFixtures = __DIR__ . '/../_files/autodiscovery';
        /** @var \Magento\Webapi\Model\Config\Reader\Soap $reader */
        $reader = $objectManager->create(
            'Magento\Webapi\Model\Config\Reader\Soap',
            array(
                'cache' => $this->getMock('Magento\Core\Model\Cache', array(), array(), '', false)
            )
        );
        $reader->setDirectoryScanner(new Zend\Code\Scanner\DirectoryScanner($pathToFixtures));
        /** Initialize SUT. */
        $this->_apiConfig = $objectManager->create('Magento\Webapi\Model\Config\Soap', array('reader' => $reader));
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
        $actualResult = $this->_helper->prepareMethodParams($class, $methodName, $requestData, $this->_apiConfig);
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
        $optionalNotSetInput = array(
            'email' => "test_email@example.com",
            'firstname' => 'firstName'
        );
        $optionalNotSetOutput = new Vendor_Module_Model_Webapi_CustomerData();
        $optionalNotSetOutput->email = "test_email@example.com";
        $optionalNotSetOutput->firstname = "firstName";
        $optionalNotSetOutput->lastname = "DefaultLastName";
        $optionalNotSetOutput->password = "123123q";

        return array(
            // Test valid data that does not need transformations.
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
            ),
            // Test filtering unnecessary data.
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV2',
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test parameters sorting.
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param4' => 4, 'param2' => 2, 'param3' => array($customerDataObject), 'param1' => 1),
                array('param1' => 1, 'param2' => 2, 'param3' => array($customerDataObject), 'param4' => 4),
            ),
            // Test default values setting.
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param1' => 1, 'param2' => 2),
                array('param1' => 1, 'param2' => 2, 'param3' => array(), 'param4' => 'default_value'),
            ),
            // Test with object instead of class name.
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'createV2',
                array('param2' => 2, 'param1' => 1),
                array('param1' => 1, 'param2' => 2),
            ),
            // Test passing of partially formatted objects.
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'updateV1',
                array('param1' => 1, 'param2' => get_object_vars($customerDataObject)),
                array('param1' => 1, 'param2' => $customerDataObject),
            ),
            // Test passing of complex type parameter with optional field not set.
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'updateV1',
                array('param1' => 1, 'param2' => $optionalNotSetInput),
                array('param1' => 1, 'param2' => $optionalNotSetOutput),
            ),
        );
    }

    /**
     * Test prepareMethodParams method with unexpected data instead of array.
     */
    public function testPrepareMethodParamsArrayExpectedException()
    {
        $this->setExpectedException(
            'Magento\Webapi\Exception',
            'Data corresponding to "VendorModuleCustomerData[]" type is expected to be an array.',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        $this->_helper->prepareMethodParams(
            'Vendor_Module_Controller_Webapi_Resource_Subresource',
            'createV1',
            array('param1' => 1, 'param2' => 2, 'param3' => 'not_array', 'param4' => 4),
            $this->_apiConfig
        );
    }

    /**
     * Test prepareMethodParams method with complex type equal to unexpected data instead of array.
     */
    public function testPrepareMethodParamsComplexTypeArrayExpectedException()
    {
        $this->setExpectedException(
            'Magento\Webapi\Exception',
            'Data corresponding to "VendorModuleCustomerData" type is expected to be an array.',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        $this->_helper->prepareMethodParams(
            'Vendor_Module_Controller_Webapi_Resource_Subresource',
            'updateV1',
            array('param1' => 1, 'param2' => 'Non array complex data'),
            $this->_apiConfig
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
        $this->setExpectedException($exceptionClass, $exceptionMessage);
        $this->_helper->prepareMethodParams($class, $methodName, $requestData, $this->_apiConfig);
    }

    public static function dataProviderForTestPrepareMethodParamsNegative()
    {
        /** Customer data without required field */
        $withoutRequired = array(
            'email' => "test_email@example.com"
        );
        return array(
            // Test exception in case of missing required parameter.
            array(
                'Vendor_Module_Controller_Webapi_Resource_Subresource',
                'createV1',
                array('param2' => 2, 'param4' => 4),
                'Magento\Webapi\Exception',
                'Required parameter "param1" is missing.'
            ),
            // Test passing of complex type parameter with not specified required field.
            array(
                new Vendor_Module_Controller_Webapi_Resource_Subresource(),
                'updateV1',
                array('param1' => 1, 'param2' => $withoutRequired),
                'Magento\Webapi\Exception',
                'Value of "firstname" attribute is required.'
            ),
        );
    }
}
