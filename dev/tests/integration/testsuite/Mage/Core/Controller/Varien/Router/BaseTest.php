<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Controller_Varien_Router_BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Varien_Router_Base
     */
    protected $_model;

    protected function setUp()
    {
        $options = array(
            'areaCode' => 'frontend',
            'baseController' => 'Mage_Core_Controller_Front_Action'
        );
        $this->_model = Mage::getModel('Mage_Core_Controller_Varien_Router_Base', $options);
        $this->_model->setFront(Mage::getModel('Mage_Core_Controller_Varien_Front'));
    }

    public function testCollectRoutes()
    {
        $this->_model->collectRoutes('frontend', 'standard');
        $this->assertEquals('catalog', $this->_model->getFrontNameByRoute('catalog'));
    }

    public function testFetchDefault()
    {
        $default = array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'index'
        );
        $this->assertEmpty($this->_model->getFront()->getDefault());
        $this->_model->fetchDefault();
        $this->assertEquals($default, $this->_model->getFront()->getDefault());
    }

    public function testMatch()
    {
        if (!Magento_Test_Helper_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get match without sending headers');
        }

        $request = new Magento_Test_Request();
        $this->assertNull($this->_model->match($request));

        $this->_model->collectRoutes('frontend', 'standard');
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Action', $this->_model->match($request));
        $request->setRequestUri('core/index/index');
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Action', $this->_model->match($request));

        $request->setPathInfo('not_exists/not_exists/not_exists')
            ->setModuleName('not_exists')
            ->setControllerName('not_exists')
            ->setActionName('not_exists');
        $this->assertNull($this->_model->match($request));
    }

    /**
     * @covers Mage_Core_Controller_Varien_Router_Base::addModule
     * @covers Mage_Core_Controller_Varien_Router_Base::getModulesByFrontName
     * @covers Mage_Core_Controller_Varien_Router_Base::getRouteByFrontName
     * @covers Mage_Core_Controller_Varien_Router_Base::getFrontNameByRoute
     */
    public function testAddModuleAndGetters()
    {
        $this->_model->addModule('test_front', 'test_name', 'test_route');
        $this->assertEquals(array('test_name'), $this->_model->getModulesByFrontName('test_front'));
        $this->assertEquals('test_route', $this->_model->getRouteByFrontName('test_front'));
        $this->assertEquals('test_front', $this->_model->getFrontNameByRoute('test_route'));
    }

    public function testGetModuleByName()
    {
        $this->assertTrue($this->_model->getModuleByName('test', array('test')));
    }

    /**
     * @covers Mage_Core_Controller_Varien_Router_Base::getControllerFileName
     * @covers Mage_Core_Controller_Varien_Router_Base::validateControllerFileName
     */
    public function testGetControllerFileName()
    {
        $file = $this->_model->getControllerFileName('Mage_Core', 'index');
        $this->assertStringEndsWith('IndexController.php', $file);
        $this->assertTrue($this->_model->validateControllerFileName($file));
        $this->assertFalse($this->_model->validateControllerFileName(''));
    }

    public function testGetControllerClassName()
    {
        $this->assertEquals('Mage_Core_IndexController', $this->_model->getControllerClassName('Mage_Core', 'index'));
    }
}
