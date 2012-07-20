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

class Mage_Api2_Model_Config_RestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Api2_Model_Config_Rest
     */
    protected static $_model = null;

    public static function setUpBeforeClass()
    {
        self::$_model = new Mage_Api2_Model_Config_Rest(glob(__DIR__ . '/_files/positive/*/rest.xml'));
    }

    /**
     * Exception should be thrown if "resource_type" attribute of route is not equal to "item" or "collection"
     *
     * @expectedException Magento_Exception
     */
    public function testRouteResourceTypeInvalidValue()
    {
        new Mage_Api2_Model_Config_Rest(glob(__DIR__ . '/_files/negative/invalid_route_resource_type.xml'));
    }

    /**
     * Exception should be thrown if there are not unique routes present in the config
     *
     * @expectedException Magento_Exception
     */
    public function testNotUniqueRouteValue()
    {
        new Mage_Api2_Model_Config_Rest(glob(__DIR__ . '/_files/negative/not_unique_routes.xml'));
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
    }

    public function testGetRoutes()
    {
        $actualRoutes = self::$_model->getRoutes();
        /** @var Mage_Api2_Controller_Router_Route_Rest $route */
        foreach ($actualRoutes as $route) {
            $this->assertInstanceOf('Mage_Api2_Controller_Router_Route_Rest', $route);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetControllerClassByResourceNameInvalidNameException()
    {
        self::$_model->getControllerClassByResourceName('invalid_resource_name');
    }

    public function testGetControllerClassByResourceName()
    {
        /** @var Mage_Api2_Controller_Router_Route_Rest $route */
        $route = current(self::$_model->getRoutes());
        $resourceName = $route->getResourceName();
        $this->assertEquals('test_module_a', $resourceName);
        $this->assertEquals('Mage_Test_Module_Api_Controller',
            self::$_model->getControllerClassByResourceName($resourceName));
    }
}