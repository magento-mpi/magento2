<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\App\Router;

/**
 * @magentoAppArea adminhtml
 */
class DefaultRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\App\Router\DefaultRouter
     */
    protected $model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create('Magento\Backend\App\Router\DefaultRouter');
    }

    public function testRouterCanProcessRequestsWithProperPathInfo()
    {
        $request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('backend/admin/dashboard'));

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
        $this->assertEquals($className, $this->model->getControllerClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return array(
            array('Magento_Index', 'process', 'Magento\Index\Controller\Adminhtml\Process'),
            array('Magento_Index_Adminhtml', 'process', 'Magento\Index\Controller\Adminhtml\Process'),
        );
    }

    /**
     * @magentoDataFixture Magento/Backend/_files/controllerIndexClass.php
     */
    public function testMatchCustomNoRouteAction()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get match without sending headers');
        }

        $routers = array(
            'testmodule' => array(
                'frontName' => 'testmodule',
                'id' => 'testmodule',
                'modules' => ['Magento_Testmodule_Adminhtml']
            )
        );

        $routeConfig = $this->getMock(
            'Magento\App\Route\Config',
            ['_getRoutes'],
            array(
                'reader' => $this->objectManager->get('Magento\App\Route\Config\Reader'),
                'cache' => $this->objectManager->get('Magento\Config\CacheInterface'),
                'configScope' => $this->objectManager->get('Magento\Config\ScopeInterface'),
                'areaList' => $this->objectManager->get('Magento\App\AreaList'),
                'cacheId' => 'RoutesConfig',
            )
        );

        $routeConfig->expects($this->any())
            ->method('_getRoutes')
            ->will($this->returnValue($routers));

        $defaultRouter = $this->objectManager->create('Magento\Backend\App\Router\DefaultRouter', array(
            'routeConfig' => $routeConfig
        ));

        /** @var $request \Magento\TestFramework\Request */
        $request = $this->objectManager->get('Magento\TestFramework\Request');

        $request->setPathInfo('backend/testmodule/test_controller');
        $controller = $defaultRouter->match($request);
        $this->assertInstanceOf('Magento\Testmodule\Controller\Adminhtml\Index', $controller);
        $this->assertEquals('noroute', $request->getActionName());
    }
}
