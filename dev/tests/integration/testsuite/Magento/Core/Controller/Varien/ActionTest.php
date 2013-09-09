<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Controller_Varien_ActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Varien_Action|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        Mage::getConfig();
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->setDefaultDesignTheme();
        $arguments = array(
            'request'  => new Magento_TestFramework_Request(),
            'response' => new Magento_TestFramework_Response(),
        );
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Controller_Varien_Action_Context', $arguments);
        $this->_model = $this->getMockForAbstractClass(
            'Magento_Core_Controller_Varien_Action',
            array($context)
        );
    }

    public function testHasAction()
    {
        $this->assertFalse($this->_model->hasAction('test'));
        $this->assertTrue($this->_model->hasAction('noroute'));
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Magento_TestFramework_Request', $this->_model->getRequest());
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('Magento_TestFramework_Response', $this->_model->getResponse());
    }

    public function testSetGetFlag()
    {
        $this->assertEmpty($this->_model->getFlag(''));

        $this->_model->setFlag('test', 'test_flag', 'test_value');
        $this->assertFalse($this->_model->getFlag('', 'test_flag'));
        $this->assertEquals('test_value', $this->_model->getFlag('test', 'test_flag'));
        $this->assertNotEmpty($this->_model->getFlag(''));

        $this->_model->setFlag('', 'test', 'value');
        $this->assertEquals('value', $this->_model->getFlag('', 'test'));
    }

    public function testGetFullActionName()
    {
        /* empty request */
        $this->assertEquals('__', $this->_model->getFullActionName());

        $this->_model->getRequest()->setRouteName('test')
            ->setControllerName('controller')
            ->setActionName('action');
        $this->assertEquals('test/controller/action', $this->_model->getFullActionName('/'));
    }

    /**
     * @param string $controllerClass
     * @param string $expectedArea
     * @dataProvider controllerAreaDesignDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetLayout($controllerClass, $expectedArea)
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Config_Scope')->setCurrentScope($expectedArea);
        /** @var $controller Magento_Core_Controller_Varien_Action */
        $controller = $objectManager->create($controllerClass);
        $this->assertInstanceOf('Magento_Core_Model_Layout', $controller->getLayout());
        $this->assertEquals($expectedArea, $controller->getLayout()->getArea());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testLoadLayout()
    {
        $this->_model->loadLayout();
        $this->assertContains('default', $this->_model->getLayout()->getUpdate()->getHandles());

        $this->_model->loadLayout('test');
        $this->assertContains('test', $this->_model->getLayout()->getUpdate()->getHandles());

        $this->assertInstanceOf('Magento_Core_Block_Abstract', $this->_model->getLayout()->getBlock('root'));
    }

    public function testGetDefaultLayoutHandle()
    {
        $this->_model->getRequest()
            ->setRouteName('Test')
            ->setControllerName('Controller')
            ->setActionName('Action');
        $this->assertEquals('test_controller_action', $this->_model->getDefaultLayoutHandle());
    }

    /**
     * @param string $route
     * @param string $controller
     * @param string $action
     * @param array $expected
     * @param array $nonExpected
     *
     * @magentoAppIsolation enabled
     * @dataProvider addActionLayoutHandlesDataProvider
     */
    public function testAddActionLayoutHandles($route, $controller, $action, $expected, $nonExpected)
    {
        $this->_model->getRequest()
            ->setRouteName($route)
            ->setControllerName($controller)
            ->setActionName($action);
        $this->_model->addActionLayoutHandles();
        $handles = $this->_model->getLayout()->getUpdate()->getHandles();

        foreach ($expected as $expectedHandle) {
            $this->assertContains($expectedHandle, $handles);
        }
        foreach ($nonExpected as $nonExpectedHandle) {
            $this->assertNotContains($nonExpectedHandle, $handles);
        }
    }

    /**
     * @return array
     */
    public function addActionLayoutHandlesDataProvider()
    {
        return array(
            array('Test', 'Controller', 'Action', array('test_controller_action'),
                array('STORE_' . Mage::app()->getStore()->getCode())
            ),
            array('catalog', 'product', 'gallery', array('catalog_product_gallery'),
                array('default', 'catalog_product_view')
            )
        );
    }

    /**
     * @param string $route
     * @param string $controller
     * @param string $action
     * @param array $expected
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture global/dev/page_type/render_inherited 1
     * @dataProvider addActionLayoutHandlesInheritedDataProvider
     */
    public function testAddActionLayoutHandlesInherited($route, $controller, $action, $expected)
    {
        $this->_model->getRequest()
            ->setRouteName($route)
            ->setControllerName($controller)
            ->setActionName($action);
        $this->_model->addActionLayoutHandles();
        $handles = $this->_model->getLayout()->getUpdate()->getHandles();
        foreach ($expected as $expectedHandle) {
            $this->assertContains($expectedHandle, $handles);
        }
    }

    /**
     * @return array
     */
    public function addActionLayoutHandlesInheritedDataProvider()
    {
        return array(
            array('test', 'controller', 'action', array('test_controller_action')),
            array('catalog', 'product', 'gallery', array('default', 'catalog_product_view', 'catalog_product_gallery'))
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAddPageLayoutHandles()
    {
        $this->_model->getRequest()->setRouteName('test')
            ->setControllerName('controller')
            ->setActionName('action');
        $result = $this->_model->addPageLayoutHandles();
        $this->assertFalse($result);
        $this->assertEmpty($this->_model->getLayout()->getUpdate()->getHandles());

        $this->_model->getRequest()->setRouteName('catalog')
            ->setControllerName('product')
            ->setActionName('view');
        $result = $this->_model->addPageLayoutHandles(array('type' => 'simple'));
        $this->assertTrue($result);
        $handles = $this->_model->getLayout()->getUpdate()->getHandles();
        $this->assertContains('default', $handles);
        $this->assertContains('catalog_product_view', $handles);
        $this->assertContains('catalog_product_view_type_simple', $handles);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testRenderLayout()
    {
        $this->_model->loadLayout();
        $this->assertEmpty($this->_model->getResponse()->getBody());
        $this->_model->renderLayout();
        $this->assertNotEmpty($this->_model->getResponse()->getBody());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDispatch()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        if (headers_sent()) {
            $this->markTestSkipped('Can\' dispatch - headers already sent');
        }
        $request = new Magento_TestFramework_Request();
        $request->setDispatched();

        $arguments = array(
            'request'  => $request,
            'response' => new Magento_TestFramework_Response(),
        );
        $context = $objectManager->create('Magento_Core_Controller_Varien_Action_Context', $arguments);

        /* Area-specific controller is used because area must be known at the moment of loading the design */
        $this->_model = $objectManager->create('Magento_Core_Controller_Front_Action',
            array('context'  => $context)
        );
        $objectManager->get('Magento_Core_Model_Config_Scope')->setCurrentScope('frontend');
        $this->_model->dispatch('not_exists');

        $this->assertFalse($request->isDispatched());
        $this->assertEquals('cms', $request->getModuleName());
        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('noRoute', $request->getActionName());
    }

    public function testGetActionMethodName()
    {
        $this->assertEquals('testAction', $this->_model->getActionMethodName('test'));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testNoCookiesAction()
    {
        $this->assertEmpty($this->_model->getResponse()->getBody());
        $this->_model->noCookiesAction();
        $redirect = array(
            'name' => 'Location',
            'value' => 'http://localhost/index.php/enable-cookies',
            'replace' => true,
        );
        $this->assertEquals($redirect, $this->_model->getResponse()->getHeader('Location'));
    }

    /**
     * @magentoConfigFixture install/design/theme/full_name magento_basic
     * @magentoConfigFixture frontend/design/theme/full_name magento_demo
     * @magentoConfigFixture adminhtml/design/theme/full_name magento_basic
     * @magentoAppIsolation enabled
     * @dataProvider controllerAreaDesignDataProvider
     *
     * @param string $controllerClass
     * @param string $expectedArea
     * @param string $expectedStore
     * @param string $expectedDesign
     * @param string $context
     */
    public function testPreDispatch($controllerClass, $expectedArea, $expectedStore, $expectedDesign, $context)
    {
        Mage::app()->loadArea($expectedArea);
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $controller Magento_Core_Controller_Varien_Action */
        $context =
        $context = $objectManager->create($context, array('response' => new Magento_TestFramework_Response()));
        $controller = $objectManager->create($controllerClass, array('context' => $context));
        $controller->preDispatch();

        $design = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface');
        $this->assertEquals($expectedArea, $design->getArea());
        $this->assertEquals($expectedStore, Mage::app()->getStore()->getCode());
        if ($expectedDesign) {
            $this->assertEquals($expectedDesign, $design->getDesignTheme()->getThemePath());
        }
    }

    /**
     * @return array
     */
    public function controllerAreaDesignDataProvider()
    {
        return array(
            'install' => array(
                'Magento_Install_Controller_Action',
                'install',
                'default',
                'magento_basic',
                'Magento_Core_Controller_Varien_Action_Context'
            ),
            'frontend' => array(
                'Magento_Core_Controller_Front_Action',
                'frontend',
                'default',
                'magento_demo',
                'Magento_Core_Controller_Varien_Action_Context'
            ),
            'backend' => array(
                'Magento_Adminhtml_Controller_Action',
                'adminhtml',
                'admin',
                'magento_basic',
                'Magento_Backend_Controller_Context'
            ),
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testNoRouteAction()
    {
        $status = 'test';
        $this->_model->getRequest()->setParam('__status__', $status);
        $caughtException = false;
        $message = '';
        try {
            $this->_model->norouteAction();
        } catch (Exception $e) {
            $caughtException = true;
            $message = $e->getMessage();
        }
        $this->assertFalse($caughtException, $message);
    }
}
