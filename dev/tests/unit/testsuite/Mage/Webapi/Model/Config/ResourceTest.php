<?php
/**
 * File with unit tests for API configuration class: Mage_Webapi_Model_Config
 *
 * @copyright {}
 */

/**#@+
 * API resources must be available without auto loader as the file name cannot be calculated from class name.
 */
require_once __DIR__ . '/../../_files/autodiscovery/resource_class_fixture.php';
require_once __DIR__ . '/../../_files/autodiscovery/subresource_class_fixture.php';
require_once __DIR__ . '/../../_files/data_types/CustomerData.php';
require_once __DIR__ . '/../../_files/data_types/Customer/AddressData.php';
require_once __DIR__ . '/_files/resource_with_invalid_interface.php';
require_once __DIR__ . '/_files/resource_with_invalid_name.php';
require_once __DIR__ . '/_files/autodiscovery/empty_var_tags/data_type.php';
require_once __DIR__ . '/_files/autodiscovery/empty_property_description/data_type.php';
/**#@-*/

/**
 * Test of API configuration class: Mage_Webapi_Model_Config
 */
class Mage_Webapi_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Config
     */
    protected static $_apiConfig;

    public static function tearDownAfterClass()
    {
        self::$_apiConfig = null;
        parent::tearDownAfterClass();
    }

    /**
     * @return Mage_Webapi_Model_Config
     */
    protected function _getModel()
    {
        if (!self::$_apiConfig) {
            $pathToResourceFixtures = __DIR__ . '/../../_files/autodiscovery';
            self::$_apiConfig = $this->_createResourceConfig($pathToResourceFixtures);
        }
        return self::$_apiConfig;
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
        $actualResourceName = $this->_getModel()->getResourceNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetResourceNameByOperationPositive()
    {
        return array(
            array('vendorModuleResourceCreate', 'v1', 'vendorModuleResource'),
            array('vendorModuleResourceCreate', '1', 'vendorModuleResource',
                "Resource was identified incorrectly by version without 'v' prefix"),
            array('vendorModuleResourceMultiUpdate', 'v2', 'vendorModuleResource',
                'Compound method names or version seem to be identified incorrectly.'),
            array('vendorModuleResourceSubresourceUpdate', 'v1', 'vendorModuleResourceSubresource',
                'Compound resource name is identified incorrectly.'),
            array('vendorModuleResourceSubresourceMultiDelete', null, 'vendorModuleResourceSubresource',
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
        $actualResourceName = $this->_getModel()->getResourceNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetResourceNameByOperationNegative()
    {
        return array(
            array('customerUpdate', 'v1', false, "In case when resource not found 'false' is expected."),
            array('vendorModuleResourceCreate', 'v100', false, "In case when version not found 'false' is expected."),
        );
    }

    /**
     * @dataProvider dataProviderTestGetResourceNameByOperationException
     * @param string $operation
     * @param string $resourceVersion
     */
    public function testGetResourceNameByOperationException($operation, $resourceVersion)
    {
        $this->setExpectedException('InvalidArgumentException',
            sprintf('The "%s" is not valid API resource operation name.', $operation));
        $this->_getModel()->getResourceNameByOperation($operation, $resourceVersion);
    }

    public function dataProviderTestGetResourceNameByOperationException()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'v2', 'Excessive suffix is ignored.'),
            array('customerInvalid', 'v1', "In case when operation not found 'false' is expected."),
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
        $actualResourceName = $this->_getModel()->getMethodNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetMethodNameByOperation()
    {
        return array(
            array('vendorModuleResourceCreate', 'v1', 'create'),
            array('vendorModuleResourceMultiUpdate', 'v2', 'multiUpdate',
                'Compound method names seem be be identified incorrectly or version processing is broken.'),
            array('vendorModuleResourceSubresourceMultiDelete', null, 'multiDelete',
                "If version is not set - no check must be performed for operation existence in resource."),
            array('vendorModuleResourceUpdate', 'v100', false, "In case when version not found 'false' is expected."),
        );
    }

    /**
     * @dataProvider dataProviderTestGetMethodNameByOperationException
     * @param string $operation
     * @param string $resourceVersion
     */
    public function testGetMethodNameByOperationException($operation, $resourceVersion)
    {
        $this->setExpectedException('InvalidArgumentException',
            sprintf('The "%s" is not valid API resource operation name.', $operation));
        $this->_getModel()->getMethodNameByOperation($operation, $resourceVersion);
    }

    public function dataProviderTestGetMethodNameByOperationException()
    {
        return array(
            array('vendorModuleResourceMultiUpdateExcessiveSuffix', 'v2', 'Excessive suffix is ignored.'),
            array('vendorModuleResourceInvalid', 'v1', "In case when operation not found 'false' is expected."),
        );
    }

    public function testGetControllerClassByOperationNamePositive()
    {
        $actualControllerClass = $this->_getModel()->getControllerClassByOperationName('vendorModuleResourceList');
        $message = 'Controller class was identified incorrectly by given operation.';
        $this->assertEquals('Vendor_Module_Webapi_ResourceController', $actualControllerClass, $message);
    }

    /**
     * @dataProvider dataProviderTestGetControllerClassByOperationNameNegative
     * @param string $operation
     */
    public function testGetControllerClassByOperationNameNegative($operation)
    {
        $this->setExpectedException('InvalidArgumentException',
            sprintf('The "%s" is not valid API resource operation name.', $operation));
        $this->_getModel()->getControllerClassByOperationName($operation);
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
        $this->_getModel()->getControllerClassByOperationName('resourceWithoutControllerAndModuleGet');
    }

    /**
     * @dataProvider dataProviderForTestGetResourceMaxVersion
     * @param string $resourceName
     * @param int $expectedMaxVersion
     */
    public function testGetResourceMaxVersion($resourceName, $expectedMaxVersion)
    {
        $this->assertEquals($expectedMaxVersion, $this->_getModel()->getResourceMaxVersion($resourceName),
            "Resource Maximum available version was identified incorrectly.");
    }

    public function dataProviderForTestGetResourceMaxVersion()
    {
        return array(
            array('vendorModuleResource', 5),
            array('vendorModuleResourceSubresource', 4),
        );
    }

    public function testGetResourceMaxVersionException()
    {
        $resourceName = 'InvalidResource';
        $this->setExpectedException('InvalidArgumentException',
            sprintf('Resource "%s" does not exist.', $resourceName));
        $this->_getModel()->getResourceMaxVersion($resourceName);
    }

    /**
     * @dataProvider dataProviderTestGenerateRestRoutesTopLevelResource
     * @param string $className
     * @param string $methodName
     * @param array $expectedRoutes
     */
    public function testGenerateRestRoutesTopLevelResource($className, $methodName, $expectedRoutes)
    {
        $actualRoutes = $this->_getModel()->generateRestRoutes($this->_createMethodReflection($className, $methodName));
        $this->assertRoutesEqual($expectedRoutes,$actualRoutes);
    }

    public static function dataProviderTestGenerateRestRoutesTopLevelResource()
    {
        $versionParam = Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION;
        $className = "Vendor_Module_Webapi_ResourceController";
        return array(
            array(
                $className,
                "createV1",
                array(
                    "/:$versionParam/vendorModuleResources/requiredField/:requiredField" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    "/:$versionParam/vendorModuleResources/requiredField/:requiredField/optionalField/:optionalField" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    "/:$versionParam/vendorModuleResources/requiredField/:requiredField/secondOptional/:secondOptional" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    "/:$versionParam/vendorModuleResources/requiredField/:requiredField/optionalField/:optionalField/secondOptional/:secondOptional" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    "/:$versionParam/vendorModuleResources/requiredField/:requiredField/secondOptional/:secondOptional/optionalField/:optionalField" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    )
                ),
            ),
            array(
                $className,
                "updateV2",
                array(
                    "/:$versionParam/vendorModuleResources/:id/additionalRequired/:additionalRequired" => array(
                        "actionType" => "item",
                        "resourceName" => "vendorModuleResource"
                    ),
                ),
            ),
            array(
                $className,
                "getV2",
                array(
                    "/:$versionParam/vendorModuleResources/:id" => array(
                        "actionType" => "item",
                        "resourceName" => "vendorModuleResource"
                    ),
                ),
            ),
            array(
                $className,
                "listV2",
                array(
                    "/:$versionParam/vendorModuleResources/additionalRequired/:additionalRequired" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    "/:$versionParam/vendorModuleResources/additionalRequired/:additionalRequired/optional/:optional" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                ),
            ),
            array(
                $className,
                "deleteV3",
                array(
                    "/:$versionParam/vendorModuleResources/:id" => array(
                        "actionType" => "item",
                        "resourceName" => "vendorModuleResource"
                    ),
                ),
            ),
            array(
                $className,
                "multiUpdateV2",
                array(
                    "/:$versionParam/vendorModuleResources" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                ),
            ),
            array(
                $className,
                "multiDeleteV2",
                array(
                    "/:$versionParam/vendorModuleResources" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
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
        $actualRoutes = $this->_getModel()->generateRestRoutes($this->_createMethodReflection($className, $methodName));
        $this->assertRoutesEqual($expectedRoutes,$actualRoutes);
    }

    public static function dataProviderTestGenerateRestRoutesSubresource()
    {
        $className = 'Vendor_Module_Webapi_Resource_SubresourceController';
        $versionParam = Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION;
        return array(
            array(
                $className,
                'createV2',
                array(
                    "/:$versionParam/vendorModuleResources/:parentId/subresources" => array(
                        'actionType' => 'collection',
                        'resourceName' => 'vendorModuleResourceSubresource'
                    )
                ),
            ),
            array(
                $className,
                'updateV1',
                array(
                    "/:$versionParam/vendorModuleResources/subresources/:id" => array(
                        'actionType' => 'item',
                        'resourceName' => 'vendorModuleResourceSubresource'
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
            "Mage_Webapi_Model_Config::generateRestRoutes() must return value of 'array' type.");

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

    public function testGenerateRestRoutesInvalidMethod()
    {
        $this->setExpectedException('InvalidArgumentException',
            '"invalidMethodNameV2" is invalid API resource method.');
        $this->_getModel()->generateRestRoutes($this->_createMethodReflection(
            'Vendor_Module_Webapi_Resource_InvalidController', 'invalidMethodNameV2'));
    }

    public function testGetResource()
    {
        $resourceData = $this->_getModel()->getResourceDataMerged('vendorModuleResource', 'v1');
        $this->assertTrue(isset($resourceData['methods']['create']), "Information about methods is not available.");
        $this->assertTrue(isset($resourceData['methods']['create']['interface']['in']['parameters']['requiredField']),
            "Data structure seems to be missing method input parameters.");
        $this->assertTrue(isset($resourceData['methods']['create']['interface']['out']['parameters']['result']['type']),
            "Data structure seems to be missing method output parameters.");
    }

    public function testGetResourceInvalidResourceName()
    {
        $this->setExpectedException('RuntimeException', 'Unknown resource "%s".');
        $this->_getModel()->getResourceDataMerged('invalidResource', 'v1');
    }

    public function testGetResourceInvalidVersion()
    {
        $this->setExpectedException('RuntimeException', 'Unknown version "%s" for resource "%s".');
        $this->_getModel()->getResourceDataMerged('vendorModuleResource', 'v100');
    }

    public function testGetDataType()
    {
        $actualDataType = $this->_getModel()->getDataType('VendorModuleCustomerAddressData');
        $expectedDataType = array(
            'documentation' => 'Tests fixture for Auto Discovery functionality. Customer address entity.',
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
        $this->_getModel()->getDataType('InvalidDataTypeName');
    }

    public function testGetBodyParamNameInvalidInterface()
    {
        $methodName = 'updateV1';
        $bodyPosition = 2;
        $this->setExpectedException('LogicException', sprintf('Method "%s" must have parameter for passing request body. '
            . 'Its position must be "%s" in method interface.', $methodName, $bodyPosition));
        $this->_getModel()->getBodyParamName($this->_createMethodReflection(
            'Vendor_Module_Webapi_Resource_InvalidController', $methodName));
    }

    public function testGetIdParamNameEmptyMethodInterface()
    {
        $this->setExpectedException('LogicException', 'must have at least one parameter: resource ID.');
        $this->_getModel()->getIdParamName($this->_createMethodReflection(
            'Vendor_Module_Webapi_Resource_InvalidController', 'emptyInterfaceV2'));
    }

    public function testGetResourceNamePartsException()
    {
        $className = 'Vendor_Module_Webapi_Resource_Invalid';
        $this->setExpectedException('InvalidArgumentException', sprintf('Invalid controller class name "%s".', $className));
        $this->_getModel()->getResourceNameParts($className);
    }

    /**
     * @dataProvider dataProviderForTestGetResourceNameParts
     * @param $className
     * @param $expectedResourceParts
     */
    public function testGetResourceNameParts($className, $expectedResourceParts)
    {
        $this->assertEquals($expectedResourceParts, $this->_getModel()->getResourceNameParts($className),
            "Resource parts for rest route were identified incorrectly.");
    }

    public static function dataProviderForTestGetResourceNameParts()
    {
        return array(
            array('Enterprise_Customer_Webapi_Customer_AddressController', array('EnterpriseCustomer', 'Address')),
            /** Check removal of 'Mage' prefix as well as duplicating parts ('Customer') */
            array('Mage_Customer_Webapi_Customer_AddressController', array('Customer', 'Address')),
        );
    }

    public function testGetIdParamException()
    {
        $className = 'Vendor_Module_Webapi_Resource_Invalid';
        $this->setExpectedException('LogicException', sprintf('"%s" is not valid resource class.', $className));
        $this->_getModel()->getIdParamName($this->_createMethodReflection($className, 'updateV1'));
    }

    public function testGetAllResourcesVersions()
    {
        $expectedResult = array(
            'vendorModuleResource' =>array('V1', 'V2', 'V3', 'V4', 'V5'),
            'vendorModuleResourceSubresource' => array('V1', 'V2', 'V4')
        );
        $allResourcesVersions = $this->_getModel()->getAllResourcesVersions();
        $this->assertEquals($expectedResult, $allResourcesVersions, "The list of all resources versions is incorrect.");
    }

    public function testGetMethodMetadataDataNotAvailable()
    {
        $this->setExpectedException('InvalidArgumentException',
            '"update" method of "vendorModuleResourceInvalid" resource in version "V2" is not registered.');
        $this->_getModel()->getMethodMetadata($this->_createMethodReflection(
            'Vendor_Module_Webapi_Resource_InvalidController', 'updateV2'));
    }

    public function testGetRestRoutes()
    {
        $actualRoutes = $this->_getModel()->getAllRestRoutes();
        $expectedRoutesCount = 16;

        /**
         * Vendor_Module_Webapi_ResourceController fixture contains two methods getV2 and deleteV3 that have
         * different names of ID param.
         * If there will be two different routes generated for these methods with different ID param names,
         * it will be impossible to identify which route should be used as they both will match the same requests.
         * E.g. DELETE /resource/:deleteId and GET /resource/:getId will match same requests.
         */
        $this->assertNotCount($expectedRoutesCount + 1, $actualRoutes,
            "Some resource methods seem to have different routes, in case when should have the same ones.");

        $this->assertCount($expectedRoutesCount, $actualRoutes, "Routes quantity does not equal to expected one.");
        /** @var $actualRoute Mage_Webapi_Controller_Router_Route_Rest */
        foreach ($actualRoutes as $actualRoute) {
            $this->assertInstanceOf('Mage_Webapi_Controller_Router_Route_Rest', $actualRoute);
        }
    }

    public function testGetRestRouteToItem()
    {
        $expectedRoute = '/:resourceVersion/vendorModuleResources/subresources/:id';
        $this->assertEquals($expectedRoute, $this->_getModel()->getRestRouteToItem('vendorModuleResourceSubresource'));
    }

    public function testGetRestRouteToItemInvalidArguments()
    {
        $resourceName = 'vendorModuleResources';
        $this->setExpectedException('InvalidArgumentException',
            sprintf('No route was found to the item of "%s" resource.', $resourceName));
        $this->_getModel()->getRestRouteToItem($resourceName);
    }

    /**
     * @dataProvider dataProviderTestTranslateArrayTypeName
     * @param string $typeToBeTranslated
     * @param string $expectedResult
     */
    public function testTranslateArrayTypeName($typeToBeTranslated, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_getModel()->translateArrayTypeName($typeToBeTranslated),
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
        $this->assertEquals($expectedResult, $this->_getModel()->translateTypeName($typeName),
            "Type translation was performed incorrectly.");
    }

    public static function dataProviderForTestTranslateTypeName()
    {
        return array(
            array('Mage_Customer_Model_Webapi_CustomerData', 'CustomerData'),
            array('Mage_Catalog_Model_Webapi_ProductData', 'CatalogProductData'),
            array('Enterprise_Customer_Model_Webapi_Customer_AddressData', 'EnterpriseCustomerAddressData'),
            array('Producer_Module_Model_Webapi_ProducerData', 'ProducerModuleProducerData'),
            array('Producer_Module_Model_Webapi_ProducerModuleData', 'ProducerModuleProducerModuleData'),
        );
    }

    public function testTranslateTypeNameInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid parameter type "Invalid_Type_Name".');
        $this->_getModel()->translateTypeName('Invalid_Type_Name');
    }

    /**
     * @dataProvider dataProviderTestGetActionTypeByMethod
     * @param string $methodName
     * @param string $expectedActionType
     */
    public function testGetActionTypeByMethod($methodName, $expectedActionType)
    {
        $this->assertEquals($expectedActionType, $this->_getModel()->getActionTypeByMethod($methodName),
            "Action type was identified incorrectly by method name.");
    }

    public static function dataProviderTestGetActionTypeByMethod()
    {
        return array(
            array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
                Mage_Webapi_Controller_Handler_Rest::ACTION_TYPE_COLLECTION),
            array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
                Mage_Webapi_Controller_Handler_Rest::ACTION_TYPE_ITEM
            ),
        );
    }

    public function testGetActionTypeException()
    {
        $methodName = 'invalidMethodV1';
        $this->setExpectedException('InvalidArgumentException',
            sprintf('"%s" method is not valid resource method.', $methodName));
        $this->_getModel()->getActionTypeByMethod($methodName);
    }

    public function testExtractDataPopulateClassException()
    {
        $this->setExpectedException('LogicException', 'There can be only one class in controller file ');
        $this->_createResourceConfig(__DIR__ . '/_files/autodiscovery/several_classes_in_one_file');
    }

    public function testExtractDataEmptyResult()
    {
        $this->setExpectedException('LogicException', 'Can not populate config - no action controllers were found.');
        $this->_createResourceConfig(__DIR__ . '/_files/autodiscovery/no_resources');
    }

    public function testExtractDataInvalidTypeOfArgument()
    {
        $this->setExpectedException('InvalidArgumentException', 'Could not load class ');
        $this->_createResourceConfig(__DIR__ . '/_files/autodiscovery/reference_to_invalid_type');
    }

    public function testExtractDataUndocumentedProperty()
    {
        $this->setExpectedException('InvalidArgumentException',
            'Each property must have description with @var annotation.');
        $this->_createResourceConfig(__DIR__ . '/_files/autodiscovery/empty_property_description');
    }

    public function testExtractDataPropertyWithoutVarTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Property type must be defined with @var tag.');
        $this->_createResourceConfig(__DIR__ . '/_files/autodiscovery/empty_var_tags');
    }

    public function testExtractDataInvalidDeprecationPolicy()
    {
        $this->setExpectedException('LogicException', '"Invalid_Deprecation_Webapi_PolicyController::getV1" '
            . 'method has invalid format of Deprecation policy. Accepted formats are createV1, '
            . 'catalogProduct::createV1 and Mage_Catalog_Webapi_ProductController::createV1.');
        $this->_createResourceConfig(__DIR__ . '/_files/autodiscovery/invalid_deprecation_policy');
    }

    public function testGetMethodRestRoutes()
    {
        $actualRoutes = $this->_getModel()->getMethodRestRoutes('vendorModuleResourceSubresource', 'create', 'v1');
        $this->assertCount(5, $actualRoutes, "Routes quantity does not match expected one.");
        foreach ($actualRoutes as $actualRoute) {
            $this->assertInstanceOf('Mage_Webapi_Controller_Router_Route_Rest', $actualRoute);
        }
    }

    public function testGetMethodRestRoutesException()
    {
        $resourceName = 'vendorModuleResourceSubresource';
        $methodName = 'multiUpdate';
        $this->setExpectedException('InvalidArgumentException',
            sprintf('"%s" resource does not have any REST routes for "%s" method.', $resourceName, $methodName));
        $this->_getModel()->getMethodRestRoutes($resourceName, $methodName, 'v1');
    }

    /**
     * @dataProvider dataProviderForTestGetDeprecationPolicy
     * @param string $resource
     * @param string $method
     * @param string $version
     * @param string $deprecationFormat
     * @param array $expectedResult
     */
    public function testGetDeprecationPolicy($resource, $method, $version, $expectedResult, $deprecationFormat)
    {
        $actualResult = $this->_getModel()->getDeprecationPolicy($resource, $method, $version);
        $this->assertEquals($expectedResult, $actualResult,
            "Deprecation policy was defined incorrectly. The following definition was used: '$deprecationFormat'");
    }

    public static function dataProviderForTestGetDeprecationPolicy()
    {
        return array(
            array('vendorModuleResource', 'list', 2,
                array(
                    'deprecated' => true,
                    'use_resource' => 'vendorModuleResource',
                    'use_method' => 'list',
                    'use_version' => 'V3'
                ),
                '@apiDeprecated vendorModuleResource::listV3'
            ),
            array('vendorModuleResource', 'list', 3, false, 'No policy defined.'),
            array('vendorModuleResource', 'delete', 'V1',
                array(
                    'removed' => true,
                    'use_resource' => 'vendorModuleResource',
                    'use_method' => 'delete',
                    'use_version' => 'V3'
                ),
                '@apiRemoved deleteV3'
            ),
            array(
                'vendorModuleResource', 'delete', 2,
                array(
                    'deprecated' => true,
                    'use_resource' => 'vendorModuleResourceSubresource',
                    'use_method' => 'delete',
                    'use_version' => 'V3'
                ),
                '@apiDeprecated Vendor_Module_Webapi_Resource_SubresourceController::deleteV3'
            ),
            array(
                'vendorModuleResource', 'delete', 'v3',
                array(
                    'removed' => true,
                ),
                '@apiDeprecated \n @apiRemoved'
            ),
            array(
                'vendorModuleResource', 'delete', 'v4',
                array(
                    'deprecated' => true,
                ),
                '@apiDeprecated'
            ),
        );
    }

    /**
     * @dataProvider dataProviderForTestGetDeprecationPolicyException
     * @param string $resource
     * @param string $method
     * @param string $version
     * @param string $expectedMessage
     */
    public function testGetDeprecationPolicyException($resource, $method, $version, $expectedMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);
        $this->_getModel()->getDeprecationPolicy($resource, $method, $version);
    }

    public static function dataProviderForTestGetDeprecationPolicyException()
    {
        return array(
            array('invalidResource', 'delete', 1, 'Unknown resource "%s".'),
            array('vendorModuleResource', 'update', 10, 'Unknown version "%s" for resource "%s".'),
            array('vendorModuleResource', 'update', 1,
                'Method "update" does not exist in "1" version of resource "vendorModuleResource".'),
        );
    }

    /**
     * Create resource config initialized with classes found in the specified directory.
     *
     * @param string $pathToDirectoryWithResources
     * @return Mage_Webapi_Model_Config
     */
    protected function _createResourceConfig($pathToDirectoryWithResources)
    {
        /** Prepare arguments for SUT constructor. */
        $objectManager = new Magento_ObjectManager_Zend();
        /** @var $appConfig Mage_Core_Model_Config */
        $appConfig = $this->getMock('Mage_Core_Model_Config', array('getNode'), array($objectManager));
        $appConfig->expects($this->once())->method('getNode')
            ->will($this->returnValue(new Mage_Core_Model_Config_Element("<empty_node></empty_node>")));
        $appConfig->setOptions(array('base_dir' => realpath(__DIR__ . "/../../../../../../../..")));
        /** Prepare mocks for SUT constructor. */
        $helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        $helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper');
        // clone is required to prevent mock object removal after test execution
        $helperClone = clone $helper;
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helperClone));
        $routeFactory = new Magento_Controller_Router_Route_Factory($objectManager);

        /** Initialize SUT. */
        $apiConfig = new Mage_Webapi_Model_Config(
            new Magento_Code_Scanner_DirectoryScanner($pathToDirectoryWithResources),
            $helperFactory,
            $appConfig,
            $this->getMockBuilder('Mage_Core_Model_Cache')->disableOriginalConstructor()->getMock(),
            new Magento_Server_Reflection(),
            $routeFactory
        );
        return $apiConfig;
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
