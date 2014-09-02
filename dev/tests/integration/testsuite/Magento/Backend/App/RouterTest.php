<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

/**
 * @magentoAppArea adminhtml
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\App\Router
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create('Magento\Backend\App\Router');
    }

    public function testRouterCanProcessRequestsWithProperPathInfo()
    {
        $request = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())->method('getPathInfo')->will($this->returnValue('backend/admin/dashboard'));

        $this->assertInstanceOf('Magento\Backend\Controller\Adminhtml\Dashboard', $this->model->match($request));
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $className
     *
     * @dataProvider getControllerClassNameDataProvider
     */
    public function testGetControllerClassName($module, $controller, $className)
    {
        $this->assertEquals($className, $this->model->getActionClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return array(
            array('Magento_Module', 'controller', 'Magento\Module\Controller\Adminhtml\Controller'),
        );
    }

    public function testMatchCustomNoRouteAction()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get match without sending headers');
        }

        $routers = array(
            'testmodule' => array(
                'frontName' => 'testfixture',
                'id' => 'testfixture',
                'modules' => array('Magento_TestFixture')
            )
        );

        $routeConfig = $this->getMock(
            'Magento\Framework\App\Route\Config',
            array('_getRoutes'),
            array(
                'reader' => $this->objectManager->get('Magento\Framework\App\Route\Config\Reader'),
                'cache' => $this->objectManager->get('Magento\Framework\Config\CacheInterface'),
                'configScope' => $this->objectManager->get('Magento\Framework\Config\ScopeInterface'),
                'areaList' => $this->objectManager->get('Magento\Framework\App\AreaList'),
                'cacheId' => 'RoutesConfig'
            )
        );

        $routeConfig->expects($this->any())->method('_getRoutes')->will($this->returnValue($routers));

        $defaultRouter = $this->objectManager->create(
            'Magento\Backend\App\Router',
            array('routeConfig' => $routeConfig)
        );

        /** @var $request \Magento\TestFramework\Request */
        $request = $this->objectManager->get('Magento\TestFramework\Request');

        $request->setPathInfo('backend/testfixture/test_controller');
        $controller = $defaultRouter->match($request);
        $this->assertInstanceOf('Magento\TestFixture\Controller\Adminhtml\Noroute', $controller);
        $this->assertEquals('noroute', $request->getActionName());
    }
}
