<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API2 config model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_ConfigTest extends Mage_PHPUnit_TestCase
{
    /**
     * API2 data helper mock
     *
     * @var Mage_Api2_Helper_Data
     */
    protected $_helperMock;

    /**
     * Config object
     *
     * @var Mage_Api2_Model_Config
     */
    protected $_config;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_helperMock = $this->getHelperMockBuilder('api2')->setMethods(array('isApiTypeSupported'))->getMock();

        Mage::setConfigModel(array('config_model' => 'Mage_Core_Model_Config_Mock'));
        Mage::getConfig()->init();

        $this->_config = new Mage_Api2_Model_Config();
    }

    /**
     * Test get resource main route
     *
     * @return void
     */
    public function testGetMainRoute()
    {
        $this->assertEquals('/products/:id', $this->_config->getMainRoute('product'));
    }

    /**
     * Test get wrong resource main route
     *
     * @return void
     */
    public function testGetMainRouteFail()
    {
        $mainRoute = $this->_config->getMainRoute('qwerty');

        $this->assertInternalType('string', $mainRoute);
        $this->assertEmpty($mainRoute, 'Main route is not empty.');
    }

    /**
     * Test get all resources from config files api2.xml
     *
     * @return void
     */
    public function testGetResources()
    {
        $resources = $this->_config->getResources();
        $this->assertInstanceOf('Varien_Simplexml_Element', $resources);

        $resources = (array) $resources;
        $this->assertEquals(array('products', 'product'), array_keys($resources));

        foreach ($resources as $resource) {
            $resource = (array) $resource;
            $this->assertArrayHasKey('type', $resource);
            $this->assertArrayHasKey('model', $resource);
            $this->assertArrayHasKey('title', $resource);
            $this->assertArrayHasKey('routes', $resource);
        }
    }

    /**
     * Get resource by type
     *
     * @return void
     */
    public function testGetResource()
    {
        $resource = $this->_config->getResource('product');
        $this->assertInstanceOf('Varien_Simplexml_Element', $resource);

        $resource = (array) $resource;
        $this->assertArrayHasKey('type', $resource);
        $this->assertArrayHasKey('model', $resource);
        $this->assertArrayHasKey('title', $resource);
        $this->assertArrayHasKey('routes', $resource);
    }

    /**
     * Get resource by type fail
     *
     * @return void
     */
    public function testGetResourceFail()
    {
        $this->assertFalse($this->_config->getResource('qwerty'));
    }

    /**
     * Test fetch all routes of the given api type from config files api2.xm
     *
     * @return void
     */
    public function testGetRoutes()
    {
        $this->_helperMock->expects($this->once())
            ->method('isApiTypeSupported')
            ->will($this->returnValue(true));

        $routes = $this->_config->getRoutes(Mage_Api2_Model_Server::API_TYPE_REST);
        $this->assertInternalType('array', $routes);
        $this->assertEquals(2, count($routes));

        /** @var $route Mage_Api2_Model_Route_Rest */
        foreach ($routes as $route) {
            $this->assertInstanceOf('Mage_Api2_Model_Route_Rest', $route);

            $defaults = $route->getDefaults();
            $this->assertArrayHasKey('model', $defaults);
            $this->assertArrayHasKey('type', $defaults);
        }
    }

    /**
     * Test failed fetch all routes of the given api type from config files api2.xm
     *
     * @return void
     */
    public function testGetRoutesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isApiTypeSupported');

        $wrongApiType = 'qwerty';
        $this->setExpectedException(
            'Mage_Api2_Exception',
            sprintf('API type "%s" is not supported', $wrongApiType),
            Mage_Api2_Model_Server::HTTP_BAD_REQUEST
        );

        $this->_config->getRoutes($wrongApiType);
    }

    /**
     * Test get resource collection
     *
     * @return void
     */
    public function testGetResourceCollection()
    {
        $this->assertEquals('products', $this->_config->getResourceCollection('product'));
        $this->assertEmpty($this->_config->getResourceCollection('products'));
    }

    /**
     * Test get resource instance
     *
     * @return void
     */
    public function testGetResourceInstance()
    {
        $this->assertEquals('product', $this->_config->getResourceInstance('products'));
        $this->assertEmpty($this->_config->getResourceInstance('product'));
    }

    /**
     * Test get resource attributes
     *
     * @return void
     */
    public function testGetResourceAttributes()
    {
        $attributes = array (
            'review_id' => 'ID',
            'content' => 'Content',
            'created_at' => 'Content',
        );
        $this->assertEquals($attributes, $this->_config->getResourceAttributes('product'));
    }

    /**
     * Test get resource attributes
     *
     * @return void
     */
    public function testGetResourceWorkingModel()
    {
        $this->assertEquals('catalog/product', $this->_config->getResourceWorkingModel('product'));
        $this->assertEquals('catalog/product', $this->_config->getResourceWorkingModel('products'));
    }
}

/**
 * Core configuration class mock
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Config_Mock extends Mage_Core_Model_Config
{
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel=null)
    {
        $configBase = new Mage_Core_Model_Config_Base();
        $configBase->loadFile(dirname(__FILE__) . '/_fixtures/xml/' . $fileName);

        return $configBase;
    }
}
