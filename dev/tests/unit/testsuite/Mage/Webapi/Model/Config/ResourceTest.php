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

include_once __DIR__ . '/../../_files/autodiscovery/resource_class_fixture.php';
include_once __DIR__ . '/../../_files/autodiscovery/subresource_class_fixture.php';
include_once __DIR__ . '/../../_files/autodiscovery/Customer/DataStructure.php';
include_once __DIR__ . '/../../_files/autodiscovery/Customer/Address/DataStructure.php';
include_once __DIR__ . '/_files/resource_with_invalid_interface.php';

class Mage_Webapi_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Config_Resource
     */
    protected $_model = null;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    protected function setUp()
    {
        $configData = include __DIR__ . '/_files/resource_config_data.php';
        $this->_helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $this->_helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_model = new Mage_Webapi_Model_Config_Resource(array(
            'directoryScanner' => new \Zend\Code\Scanner\DirectoryScanner(),
            'applicationConfig' => new Mage_Core_Model_Config(),
            'data' => $configData,
            'helper' => $this->_helper
        ));
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_helper);
        parent::tearDown();
    }

    /**
     * @dataProvider dataProviderTestGetResourceNameByOperationPositive
     * @param string $operation
     * @param string $resourceVersion
     * @param string $expectedResourceName
     * @param string $message
     */
    public function testGetResourceNameByOperationPositive($operation, $resourceVersion, $expectedResourceName,
        $message = 'Resource name was identified incorrectly by given operation.'
    ) {
        $actualResourceName = $this->_model->getResourceNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetResourceNameByOperationPositive()
    {
        return array(
            array('customerUpdate', 'v1', 'customer'),
            array('customerUpdate', '1', 'customer',
                "Resource was identified incorrectly by version without 'v' prefix"),
            array('customerMultiUpdate', 'v1', 'customer', 'Compound method names seem be be identified incorrectly.'),
            array('enterpriseCatalogProductGet', 'v1', 'enterpriseCatalogProduct',
                'Compound resource name is identified incorrectly.'),
            array('customerMultiDelete', 'v2', 'customer', 'Version seems to be processed incorrectly.'),
            array('customerMultiDelete', null, 'customer',
                "If version is not set - no check must be performed for operation existence in resource."),
        );
    }

    /**
     * @dataProvider dataProviderTestGetResourceNameByOperationNegative
     * @param string $operation
     * @param string $resourceVersion
     * @param string $expectedResourceName
     * @param string $message
     */
    public function testGetResourceNameByOperationNegative($operation, $resourceVersion, $expectedResourceName,
        $message = 'Resource name was identified incorrectly by given operation.'
    ) {
        $actualResourceName = $this->_model->getResourceNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetResourceNameByOperationNegative()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'v2', false, 'Excessive suffix is ignored.'),
            array('customerInvalid', 'v1', false, "In case when operation not found 'false' is expected."),
            array('customerUpdate', 'v100', false, "In case when version not found 'false' is expected."),
        );
    }

    /**
     * @dataProvider dataProviderTestGetMethodNameByOperation
     * @param string $operation
     * @param string $resourceVersion
     * @param string $expectedResourceName
     * @param string $message
     */
    public function testGetMethodNameByOperation($operation, $resourceVersion, $expectedResourceName,
        $message = 'Resource name was identified incorrectly by given operation.'
    ) {
        $actualResourceName = $this->_model->getMethodNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetMethodNameByOperation()
    {
        return array(
            array('customerUpdate', 'v1', 'update'),
            array('customerMultiUpdate', 'v1', 'multiUpdate',
                'Compound method names seem be be identified incorrectly.'),
            array('enterpriseCatalogProductGet', 'v1', 'get',
                'Compound resource name is identified incorrectly.'),
            array('customerMultiDelete', 'v2', 'multiDelete', 'Version seems to be processed incorrectly.'),
            array('customerMultiDeleteExcessiveSuffix', 'v2', false, 'Excessive suffix is ignored.'),
            array('customerInvalid', 'v1', false, "In case when operation not found 'false' is expected."),
            array('customerUpdate', 'v100', false, "In case when version not found 'false' is expected."),
        );
    }

    public function testGetControllerClassByOperationNamePositive()
    {
        $actualControllerClass = $this->_model->getControllerClassByOperationName('enterpriseCatalogProductGet');
        $message = 'Controller class was identified incorrectly by given operation.';
        $this->assertEquals('Enterprise_Catalog_Webapi_ProductController', $actualControllerClass, $message);
    }

    /**
     * @dataProvider dataProviderTestGetControllerClassByOperationNameNegative
     * @param string $operation
     * @param string $message
     */
    public function testGetControllerClassByOperationNameNegative($operation, $message)
    {
        $actualControllerClass = $this->_model->getControllerClassByOperationName($operation);
        $this->assertEquals(false, $actualControllerClass, $message);
    }

    public function dataProviderTestGetControllerClassByOperationNameNegative()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'Excessive suffix is ignored.'),
            array('customerInvalid', "In case when operation not found 'false' is expected."),
        );
    }

    public function testGetControllerClassByOperationNameWithException()
    {
        $this->setExpectedException('LogicException',
            'Resource "resourceWithoutControllerAndModule" must have associated controller class.');
        $this->_model->getControllerClassByOperationName('resourceWithoutControllerAndModuleGet');
    }

    public function testGetModuleByOperationNamePositive()
    {
        $actualModuleName = $this->_model->getModuleNameByOperation('enterpriseCatalogProductGet');
        $message = 'Module name was identified incorrectly by given operation.';
        $this->assertEquals('Enterprise_Catalog', $actualModuleName, $message);
    }

    /**
     * @dataProvider dataProviderTestGetModuleByOperationNameNegative
     * @param string $operation
     * @param string $message
     */
    public function testGetModuleByOperationNameNegative($operation, $message)
    {
        $actualModuleName = $this->_model->getModuleNameByOperation($operation);
        $this->assertEquals(false, $actualModuleName, $message);
    }

    public function dataProviderTestGetModuleByOperationNameNegative()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'Excessive suffix is ignored.'),
            array('customerInvalid', "In case when operation not found 'false' is expected."),
        );
    }

    public function testGetModuleByOperationNameWithException()
    {
        $this->setExpectedException('LogicException',
            'Resource "resourceWithoutControllerAndModule" must have module specified.');
        $this->_model->getModuleNameByOperation('resourceWithoutControllerAndModuleGet');
    }

    /**
     * @dataProvider dataProviderTestGenerateRestRoutesTopLevelResource
     * @param string $className
     * @param string $methodName
     * @param array $expectedRoutes
     */
    public function testGenerateRestRoutesTopLevelResource($className, $methodName, $expectedRoutes)
    {
        $actualRoutes = $this->_model->generateRestRoutes($this->_createMethodReflection($className, $methodName));
        $this->assertRoutesEqual($expectedRoutes,$actualRoutes);
    }

    public static function dataProviderTestGenerateRestRoutesTopLevelResource()
    {
        $className = 'Vendor_Module_Webapi_ResourceController';
        return array(
            array(
                $className,
                'createV1',
                array(
                    '/v1/vendorModuleResources/requiredField/:requiredField' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v1'
                    ),
                    '/v1/vendorModuleResources/requiredField/:requiredField/optionalField/:optionalField' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v1'
                    ),
                    '/v1/vendorModuleResources/requiredField/:requiredField/optionalField/:optionalField/secondOptional/:secondOptional' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v1'
                    )
                ),
            ),
            array(
                $className,
                'updateV2',
                array(
                    '/v2/vendorModuleResources/:resourceId/additionalRequired/:additionalRequired' => array(
                        'action_type' => 'item',
                        'resource_version' => 'v2'
                    ),
                ),
            ),
            array(
                $className,
                'getV2',
                array(
                    '/v2/vendorModuleResources/:resourceId' => array(
                        'action_type' => 'item',
                        'resource_version' => 'v2'
                    ),
                ),
            ),
            array(
                $className,
                'listV2',
                array(
                    '/v2/vendorModuleResources/additionalRequired/:additionalRequired' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v2'
                    ),
                    '/v2/vendorModuleResources/additionalRequired/:additionalRequired/optional/:optional' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v2'
                    ),
                ),
            ),
            array(
                $className,
                'deleteV3',
                array(
                    '/v3/vendorModuleResources/:resourceId' => array(
                        'action_type' => 'item',
                        'resource_version' => 'v3'
                    ),
                ),
            ),
            array(
                $className,
                'multiUpdateV2',
                array(
                    '/v2/vendorModuleResources' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v2'
                    ),
                ),
            ),
            array(
                $className,
                'multiDeleteV2',
                array(
                    '/v2/vendorModuleResources' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v2'
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider dataProviderTestGenerateRestRoutesSubresource
     * @param string $className
     * @param string $methodName
     * @param array $expectedRoutes
     */
    public function testGenerateRestRoutesSubresource($className, $methodName, $expectedRoutes)
    {
        $actualRoutes = $this->_model->generateRestRoutes($this->_createMethodReflection($className, $methodName));
        $this->assertRoutesEqual($expectedRoutes,$actualRoutes);
    }

    public static function dataProviderTestGenerateRestRoutesSubresource()
    {
        $className = 'Vendor_Module_Webapi_Resource_SubresourceController';
        return array(
            array(
                $className,
                'createV2',
                array(
                    '/v2/vendorModuleResources/:param1/subresources' => array(
                        'action_type' => 'collection',
                        'resource_version' => 'v2'
                    )
                ),
            ),
            array(
                $className,
                'updateV1',
                array(
                    '/v1/vendorModuleResources/subresources/:param1' => array(
                        'action_type' => 'item',
                        'resource_version' => 'v1'
                    )
                ),
            ),
        );
    }

    /**
     * Check if list of REST routes are equal.
     *
     * @param array $expectedRoutes
     * @param array $actualRoutes
     */
    public function assertRoutesEqual($expectedRoutes, $actualRoutes)
    {
        $this->assertInternalType('array', $actualRoutes,
            "Mage_Webapi_Model_Config_Resource::generateRestRoutes() must return value of 'array' type.");

        foreach ($expectedRoutes as $expectedRoute => $expectedRouteMetadata) {
            $this->assertArrayHasKey($expectedRoute, $actualRoutes,
                "'$expectedRoute' route was expected to be present in results.");
        }
        foreach ($actualRoutes as $actualRoute => $actualRouteMetadata) {
            $this->assertArrayHasKey($actualRoute, $expectedRoutes,
                "'$actualRoute' route was not expected to be present in results.");
            $this->assertEquals($expectedRoutes[$actualRoute], $actualRouteMetadata,
                "'$actualRoute' route metadata is invalid.");
        }
    }

    public function testGetResource()
    {
        $resourceData = $this->_model->getResource('catalogProduct', 'v1');
        $expectedData = array(
            'methods' => array(
                'get' => array(
                    'documentation' => 'Core product get.',
                    'interface' => array(
                        'in' => array(
                            'parameters' => array(
                                'id' => array(
                                    'type' => 'string',
                                    'required' => true,
                                    'documentation' => '',
                                ),
                            ),
                        ),
                        'out' => array(
                            'result' => array(
                                'type' => 'array',
                                'documentation' => '',
                            ),
                        ),
                    ),
                ),
            )
        );
        $this->assertEquals($expectedData, $resourceData);
    }

    public function testGetResourceInvalidResourceName()
    {
        $this->setExpectedException('RuntimeException', 'Unknown resource "%s".');
        $this->_model->getResource('invalidResource', 'v1');
    }

    public function testGetResourceInvalidVersion()
    {
        $this->setExpectedException('RuntimeException', 'Unknown version "%s" for resource "%s".');
        $this->_model->getResource('catalogProduct', 'v100');
    }

    public function testGetDataType()
    {
        $actualDataType = $this->_model->getDataType('VendorModuleCustomerAddressDataStructure');
        $expectedDataType = array(
            'documentation' => 'Tests fixture for Auto Discovery functionality.',
            'parameters' => array(
                'street' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => NULL,
                    'documentation' => 'Street',
                ),
                'city' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => NULL,
                    'documentation' => 'City',
                ),
                'state' => array(
                    'type' => 'string',
                    'required' => false,
                    'default' => NULL,
                    'documentation' => 'State',
                ),
            ),
        );
        $this->assertEquals($expectedDataType, $actualDataType);
    }

    public function testGetDataTypeInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException',
            'Data type "InvalidDataTypeName" was not found in config.');
        $this->_model->getDataType('InvalidDataTypeName');
    }

    public function testGetBodyParamNameInvalidInterface()
    {
        $methodName = 'updateV1';
        $bodyPosition = 2;
        $this->setExpectedException('LogicException', sprintf('Method "%s" must have parameter for passing request body. '
            . 'Its position must be "%s" in method interface.', $methodName, $bodyPosition));
        $this->_model->getBodyParamName($this->_createMethodReflection(
            'Vendor_Module_Webapi_Resource_InvalidController', $methodName));
    }

    public function testGetIdParamNameInvalidMethodInterface()
    {
        $this->setExpectedException('LogicException', 'must have at least one parameter: resource ID.');
        $this->_model->getIdParamName($this->_createMethodReflection(
            'Vendor_Module_Webapi_Resource_InvalidController', 'updateV2'));
    }

    public function testGetMethodMetadataDataNotAvailable()
    {
        $this->setExpectedException('InvalidArgumentException',
            '"create" method of "vendorModuleResource" resource in version "v1" is not registered.');
        $this->_model->getMethodMetadata($this->_createMethodReflection('Vendor_Module_Webapi_ResourceController',
            'createV1'));
    }

    public function testGetRestRoutes()
    {
        $actualRoutes = $this->_model->getRestRoutes();
        $expectedRoutesCount = 2;
        $this->assertCount($expectedRoutesCount, $actualRoutes, "Routes quantity does not equal to expected one.");
        /** @var $actualRoute Mage_Webapi_Controller_Router_Route_Rest */
        foreach ($actualRoutes as $actualRoute) {
            $this->assertInstanceOf('Mage_Webapi_Controller_Router_Route_Rest', $actualRoute);
        }
    }

    public function testGetRestRouteToItem()
    {
        $expectedRoute = '/v2/catalogProducts/:resourceId';
        $this->assertEquals($expectedRoute, $this->_model->getRestRouteToItem('catalogProduct', 'v2'));
    }

    /**
     * @dataProvider dataProviderForTestGetRestRouteByResourceInvalidArguments
     */
    public function testGetRestRouteToItemInvalidArguments($resourceName, $resourceVersion)
    {
        $this->setExpectedException('InvalidArgumentException',
            sprintf('No route was found to the item of "%s" resource with "%s" version.',
            $resourceName, $resourceVersion));
        $this->_model->getRestRouteToItem($resourceName, $resourceVersion);
    }

    public static function dataProviderForTestGetRestRouteByResourceInvalidArguments()
    {
        return array(
            array('catalogProduct', 'v1'),
            array('invalidResource', 'v2'),
        );
    }

    /**
     * @dataProvider dataProviderTestTranslateArrayTypeName
     * @param string $typeToBeTranslated
     * @param string $expectedResult
     */
    public function testTranslateArrayTypeName($typeToBeTranslated, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->translateArrayTypeName($typeToBeTranslated),
            "Array type was translated incorrectly.");
    }

    public static function dataProviderTestTranslateArrayTypeName()
    {
        return array(
            array('ComplexType[]', 'ArrayOfComplexType'),
            array('string[]', 'ArrayOfString'),
            array('integer[]', 'ArrayOfInt'),
            array('bool[]', 'ArrayOfBoolean'),
        );
    }

    /**
     * @dataProvider dataProviderForTestTranslateTypeName
     * @param string $typeName
     * @param string $expectedResult
     */
    public function testTranslateTypeName($typeName, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->translateTypeName($typeName),
            "Type translation was performed incorrectly.");
    }

    public static function dataProviderForTestTranslateTypeName()
    {
        return array(
            array('Mage_Customer_Webapi_Customer_DataStructure', 'CustomerDataStructure'),
            array('Mage_Catalog_Webapi_Product_DataStructure', 'CatalogProductDataStructure'),
            array('Enterprise_Customer_Webapi_Customer_Address_DataStructure',
                'EnterpriseCustomerAddressDataStructure'),
        );
    }

    public function testTranslateTypeNameInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid parameter type "Invalid_Type_Name".');
        $this->_model->translateTypeName('Invalid_Type_Name');
    }

    /**
     * Create Zend method reflection object.
     *
     * @param string|object $classOrObject
     * @param string $methodName
     * @return Zend\Server\Reflection\ReflectionMethod
     */
    protected function _createMethodReflection($classOrObject, $methodName)
    {
        $methodReflection = new \ReflectionMethod($classOrObject, $methodName);
        $classReflection = new \ReflectionClass($classOrObject);
        $zendClassReflection = new Zend\Server\Reflection\ReflectionClass($classReflection);
        $zendMethodReflection = new Zend\Server\Reflection\ReflectionMethod($zendClassReflection, $methodReflection);
        return $zendMethodReflection;
    }
}
