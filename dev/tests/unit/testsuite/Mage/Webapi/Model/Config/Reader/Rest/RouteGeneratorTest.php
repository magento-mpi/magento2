<?php
/**
 * File with unit tests for REST routes generator class: Mage_Webapi_Model_Config_Reader_Rest_RouteGenerator
 *
 * @copyright {}
 */

/**#@+
 * API resources must be available without auto loader as the file name cannot be calculated from class name.
 */
require_once __DIR__ . '/../../../../_files/autodiscovery/resource_class_fixture.php';
require_once __DIR__ . '/../../../../_files/autodiscovery/subresource_class_fixture.php';
require_once __DIR__ . '/../../../_files/resource_with_invalid_interface.php';
require_once __DIR__ . '/../../../_files/resource_with_invalid_name.php';

/**#@-*/

class Mage_Webapi_Model_Config_Reader_Rest_RouteGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Config_Reader_Rest_RouteGenerator
     */
    protected $_model;

    protected function setUp()
    {
        $helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $this->_model = new Mage_Webapi_Model_Config_Reader_Rest_RouteGenerator($helper);
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
        $this->assertRoutesEqual($expectedRoutes, $actualRoutes);
    }

    public static function dataProviderTestGenerateRestRoutesTopLevelResource()
    {
        $versionParam = Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION;
        $className = "Vendor_Module_Controller_Webapi_Resource";
        $createPath = "/:$versionParam/vendorModuleResources/requiredField/:requiredField";
        return array(
            array(
                $className,
                "createV1",
                array(
                    $createPath => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    $createPath . "/optionalField/:optionalField" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    $createPath . "/secondOptional/:secondOptional" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    $createPath . "/optionalField/:optionalField/secondOptional/:secondOptional" => array(
                        "actionType" => "collection",
                        "resourceName" => "vendorModuleResource"
                    ),
                    $createPath . "/secondOptional/:secondOptional/optionalField/:optionalField" => array(
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
                    "/:$versionParam/vendorModuleResources/additionalRequired/:additionalRequired/"
                        . "optional/:optional" => array(
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
        $actualRoutes = $this->_model->generateRestRoutes($this->_createMethodReflection($className, $methodName));
        $this->assertRoutesEqual($expectedRoutes, $actualRoutes);
    }

    public static function dataProviderTestGenerateRestRoutesSubresource()
    {
        $className = 'Vendor_Module_Controller_Webapi_Resource_Subresource';
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function assertRoutesEqual($expectedRoutes, $actualRoutes)
    {
        $this->assertInternalType(
            'array',
            $actualRoutes,
            "Mage_Webapi_Model_Config::generateRestRoutes() must return value of 'array' type."
        );

        foreach ($expectedRoutes as $expectedRoute => $expectedMetadata) {
            $this->assertArrayHasKey(
                $expectedRoute,
                $actualRoutes,
                "'$expectedRoute' route was expected to be present in results."
            );
        }
        foreach ($actualRoutes as $actualRoute => $actualRouteMetadata) {
            $this->assertArrayHasKey(
                $actualRoute,
                $expectedRoutes,
                "'$actualRoute' route was not expected to be present in results."
            );
            $this->assertEquals(
                $expectedRoutes[$actualRoute],
                $actualRouteMetadata,
                "'$actualRoute' route metadata is invalid."
            );
        }
    }

    public function testGenerateRestRoutesInvalidMethod()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            '"invalidMethodNameV2" is an invalid API resource method.'
        );
        $this->_model->generateRestRoutes(
            $this->_createMethodReflection(
                'Vendor_Module_Controller_Webapi_Invalid_Interface',
                'invalidMethodNameV2'
            )
        );
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
