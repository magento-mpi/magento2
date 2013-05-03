<?php
/**
 * Config helper tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Class implements tests for Mage_Webapi_Config class.
 */
class Mage_Webapi_ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManager = Mage::getObjectManager();
        $this->_helper = $objectManager->get('Mage_Webapi_Config');
        parent::setUp();
    }

    public function testGetServices ()
    {
        $this->_helper->getServices();
    }

    public function testGetRestRoutes ()
    {
        $routes = $this->_helper->getRestRoutes('GET');
        echo "testGetRestRoutes\n";
        print_r($routes);
    }

}
