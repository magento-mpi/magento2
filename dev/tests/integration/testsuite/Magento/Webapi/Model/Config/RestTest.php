<?php
/**
 * File with unit tests for API configuration class: Magento_Webapi_Model_Config_Rest.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**#@+
 * API resources must be available without auto loader as the file name cannot be calculated from class name.
 */
require_once __DIR__ . '/../../_files/autodiscovery/resource_class_fixture.php';
require_once __DIR__ . '/../../_files/autodiscovery/subresource_class_fixture.php';
require_once __DIR__ . '/../../_files/data_types/CustomerData.php';
require_once __DIR__ . '/../../_files/data_types/Customer/AddressData.php';
require_once __DIR__ . '/../_files/resource_with_invalid_interface.php';
require_once __DIR__ . '/../_files/resource_with_invalid_name.php';
/**#@-*/


/**
 * Test of API configuration class: Magento_Webapi_Model_Config.
 */
class Magento_Webapi_Model_Config_RestTest extends PHPUnit_Framework_TestCase
{
    const WEBAPI_AREA_FRONT_NAME = 'webapi';

    /**
     * @var Magento_Webapi_Model_Config_Rest
     */
    protected $_apiConfig;

    /**
     * App mock clone usage helps to improve performance. It is required because mock will be removed in tear down.
     *
     * @var Magento_Core_Model_App
     */
    protected $_appClone;

    protected function setUp()
    {
        $pathToFixtures = __DIR__ . '/../../_files/autodiscovery';
        $this->_apiConfig = $this->_createResourceConfig($pathToFixtures);
    }

    public function testGetAllResourcesVersions()
    {
        $expectedResult = array(
            'vendorModuleResource' => array('V1', 'V2', 'V3', 'V4', 'V5'),
            'vendorModuleResourceSubresource' => array('V1', 'V2', 'V4')
        );
        $allResourcesVersions = $this->_apiConfig->getAllResourcesVersions();
        $this->assertEquals($expectedResult, $allResourcesVersions, "The list of all resources versions is incorrect.");
    }

    public function testGetMethodMetadataDataNotAvailable()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'The "update" method of "vendorModuleInvalidInterface" resource in version "V2" is not registered.'
        );
        $this->_apiConfig->getMethodMetadata(
            $this->_createMethodReflection(
                'Vendor_Module_Controller_Webapi_Invalid_Interface',
                'updateV2'
            )
        );
    }

    public function testGetRestRoutes()
    {
        $actualRoutes = $this->_apiConfig->getAllRestRoutes();
        $expectedRoutesCount = 16;

        /**
         * Vendor_Module_Controller_Webapi_Resource fixture contains two methods getV2 and deleteV3 that have
         * different names of ID param.
         * If there are two different routes generated for these methods with different ID param names,
         * it will be impossible to identify which route should be used as they both will match the same requests.
         * E.g. DELETE /resource/:deleteId and GET /resource/:getId will match the same requests.
         */
        $this->assertNotCount(
            $expectedRoutesCount + 1,
            $actualRoutes,
            "Some resource methods seem to have different routes, in case when should have the same ones."
        );

        $this->assertCount($expectedRoutesCount, $actualRoutes, "Routes quantity is not equal to expected one.");
        /** @var $actualRoute Magento_Webapi_Controller_Router_Route_Rest */
        foreach ($actualRoutes as $actualRoute) {
            $this->assertInstanceOf('Magento_Webapi_Controller_Router_Route_Rest', $actualRoute);
        }
    }

    public function testGetRestRouteToItem()
    {
        $expectedRoute = '/:resourceVersion/vendorModuleResources/subresources/:id';
        $this->assertEquals($expectedRoute, $this->_apiConfig->getRestRouteToItem('vendorModuleResourceSubresource'));
    }

    public function testGetRestRouteToItemInvalidArguments()
    {
        $resourceName = 'vendorModuleResources';
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf('No route to the item of "%s" resource was found.', $resourceName)
        );
        $this->_apiConfig->getRestRouteToItem($resourceName);
    }

    public function testGetMethodRestRoutes()
    {
        $actualRoutes = $this->_apiConfig->getMethodRestRoutes('vendorModuleResourceSubresource', 'create', 'v1');
        $this->assertCount(5, $actualRoutes, "Routes quantity does not match expected one.");
        foreach ($actualRoutes as $actualRoute) {
            $this->assertInstanceOf('Magento_Webapi_Controller_Router_Route_Rest', $actualRoute);
        }
    }

    public function testGetMethodRestRoutesException()
    {
        $resourceName = 'vendorModuleResourceSubresource';
        $methodName = 'multiUpdate';
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf('"%s" resource does not have any REST routes for "%s" method.', $resourceName, $methodName)
        );
        $this->_apiConfig->getMethodRestRoutes($resourceName, $methodName, 'v1');
    }

    /**
     * Create resource config initialized with classes found in the specified directory.
     *
     * @param string $pathToResources
     * @return Magento_Webapi_Model_Config_Rest
     */
    protected function _createResourceConfig($pathToResources)
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        /** Prepare arguments for SUT constructor. */
        /** @var Magento_Core_Model_CacheInterface $cache */
        $cache = $this->getMock('Magento_Core_Model_CacheInterface');
        $configMock = $this->getMockBuilder('Magento_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $configMock->expects($this->any())->method('getAreaFrontName')->will(
            $this->returnValue(self::WEBAPI_AREA_FRONT_NAME)
        );
        $appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();
        $appMock->expects($this->any())->method('getConfig')->will($this->returnValue($configMock));
        $this->_appClone = clone $appMock;
        $objectManager->configure(array(
            'Magento_Webapi_Model_Config_Reader_Rest' => array(
                'parameters' => array(
                    'cache' => $cache
                )
            )
        ));
        /** @var Magento_Webapi_Model_Config_Reader_Rest $reader */
        $reader = $objectManager->get('Magento_Webapi_Model_Config_Reader_Rest');
        $reader->setDirectoryScanner(new Zend\Code\Scanner\DirectoryScanner($pathToResources));

        /** Initialize SUT. */
        $apiConfig = $objectManager->create(
            'Magento_Webapi_Model_Config_Rest',
            array('reader' => $reader, 'application' => $this->_appClone)
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
