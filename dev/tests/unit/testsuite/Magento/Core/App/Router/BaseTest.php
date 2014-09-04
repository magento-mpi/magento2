<?php
/**
 * Tests Magento\Core\App\Router\Base
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\App\Router;

class BaseTest extends \Magento\Test\BaseTestCase
{
    /**
     * @var \Magento\Core\App\Router\Base
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\RequestInterface
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Route\ConfigInterface
     */
    private $routeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\State
     */
    private $appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Router\ActionList
     */
    private $actionListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\ActionFactory
     */
    private $actionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Code\NameBuilder
     */
    private $nameBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\DefaultPathInterface
     */
    private $defaultPathMock;

    public function setUp()
    {
        parent::setUp();
        // Create mocks
        $requestMethods = [
            'getActionName',
            'getModuleName',
            'getParam',
            'setActionName',
            'setModuleName',
            'setRouteName',
            'getPathInfo',
            'getControllerName',
            'setControllerName',
            'setControllerModule',
            'setAlias',
            'getCookie',
            'getOriginalPathInfo',
            'getPost',
            'isSecure',
            'setParams',
        ];

        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface', $requestMethods);
        $this->routeConfigMock = $this->basicMock('Magento\Framework\App\Route\ConfigInterface');
        $this->appStateMock = $this->basicMock('Magento\Framework\App\State');
        $this->actionListMock = $this->basicMock('Magento\Framework\App\Router\ActionList');
        $this->actionFactoryMock = $this->basicMock('Magento\Framework\App\ActionFactory');
        $this->nameBuilderMock = $this->basicMock('Magento\Framework\Code\NameBuilder');
        $this->defaultPathMock = $this->basicMock('Magento\Framework\App\DefaultPathInterface');

        // Prepare SUT
        $mocks = [
            'actionList' => $this->actionListMock,
            'actionFactory' => $this->actionFactoryMock,
            'routeConfig' => $this->routeConfigMock,
            'appState' => $this->appStateMock,
            'nameBuilder' => $this->nameBuilderMock,
            'defaultPath' => $this->defaultPathMock,
            'applyNoRoute'
        ];
        $this->model = $this->objectManager->getObject('Magento\Core\App\Router\Base', $mocks);
    }

    public function testMatch()
    {
        // Test Data
        $actionInstance = 'action instance';
        $moduleFrontName = 'module front name';
        $actionPath = 'action path';
        $actionName = 'action name';
        $actionClassName = 'Magento\Cms\Controller\Index\Index';
        $moduleName = 'module name';
        $moduleList = [$moduleName];

        // Stubs
        $this->basicStub($this->requestMock, 'getModuleName')->willReturn($moduleFrontName);
        $this->basicStub($this->requestMock, 'getControllerName')->willReturn($actionPath);
        $this->basicStub($this->requestMock, 'getActionName')->willReturn($actionName);
        $this->basicStub($this->routeConfigMock, 'getModulesByFrontName')->willReturn($moduleList);
        $this->basicStub($this->appStateMock, 'isInstalled')->willReturn(true);
        $this->basicStub($this->actionListMock, 'get')->willReturn($actionClassName);
        $this->basicStub($this->actionFactoryMock, 'create')->willReturn($actionInstance);

        // Expectations and Test
        $this->requestExpects('setModuleName', $moduleFrontName)
            ->requestExpects('setControllerName', $actionPath)
            ->requestExpects('setActionName', $actionName)
            ->requestExpects('setControllerModule', $moduleName);

        $this->assertSame($actionInstance, $this->model->match($this->requestMock));
    }

    public function testMatchUseParams()
    {
        // Test Data
        $actionInstance = 'action instance';
        $moduleFrontName = 'module front name';
        $actionPath = 'action path';
        $actionName = 'action name';
        $actionClassName = 'Magento\Cms\Controller\Index\Index';
        $moduleName = 'module name';
        $moduleList = [$moduleName];
        $paramList = $moduleFrontName . '/' . $actionPath . '/' . $actionName . '/key/val/key2/val2/';

        // Stubs
        $this->basicStub($this->requestMock, 'getPathInfo')->willReturn($paramList);
        $this->basicStub($this->routeConfigMock, 'getModulesByFrontName')->willReturn($moduleList);
        $this->basicStub($this->appStateMock, 'isInstalled')->willReturn(false);
        $this->basicStub($this->actionListMock, 'get')->willReturn($actionClassName);
        $this->basicStub($this->actionFactoryMock, 'create')->willReturn($actionInstance);

        // Expectations and Test
        $this->requestExpects('setModuleName', $moduleFrontName)
            ->requestExpects('setControllerName', $actionPath)
            ->requestExpects('setActionName', $actionName)
            ->requestExpects('setControllerModule', $moduleName);

        $this->assertSame($actionInstance, $this->model->match($this->requestMock));
    }

    public function testMatchUseDefaultPath()
    {
        // Test Data
        $actionInstance = 'action instance';
        $moduleFrontName = 'module front name';
        $actionPath = 'action path';
        $actionName = 'action name';
        $actionClassName = 'Magento\Cms\Controller\Index\Index';
        $moduleName = 'module name';
        $moduleList = [$moduleName];

        // Stubs
        $defaultReturnMap = [
            ['module', $moduleFrontName],
            ['controller', $actionPath],
            ['action', $actionName]
        ];
        $this->basicStub($this->defaultPathMock, 'getPart')->willReturnMap($defaultReturnMap);
        $this->basicStub($this->routeConfigMock, 'getModulesByFrontName')->willReturn($moduleList);
        $this->basicStub($this->appStateMock, 'isInstalled')->willReturn(false);
        $this->basicStub($this->actionListMock, 'get')->willReturn($actionClassName);
        $this->basicStub($this->actionFactoryMock, 'create')->willReturn($actionInstance);

        // Expectations and Test
        $this->requestExpects('setModuleName', $moduleFrontName)
            ->requestExpects('setControllerName', $actionPath)
            ->requestExpects('setActionName', $actionName)
            ->requestExpects('setControllerModule', $moduleName);

        $this->assertSame($actionInstance, $this->model->match($this->requestMock));
    }

    public function testMatchEmptyModuleList()
    {
        // Test Data
        $actionInstance = 'action instance';
        $moduleFrontName = 'module front name';
        $actionPath = 'action path';
        $actionName = 'action name';
        $actionClassName = 'Magento\Cms\Controller\Index\Index';
        $emptyModuleList = [];

        // Stubs
        $this->basicStub($this->requestMock, 'getModuleName')->willReturn($moduleFrontName);
        $this->basicStub($this->routeConfigMock, 'getModulesByFrontName')->willReturn($emptyModuleList);
        $this->basicStub($this->requestMock, 'getControllerName')->willReturn($actionPath);
        $this->basicStub($this->requestMock, 'getActionName')->willReturn($actionName);
        $this->basicStub($this->appStateMock, 'isInstalled')->willReturn(false);
        $this->basicStub($this->actionListMock, 'get')->willReturn($actionClassName);
        $this->basicStub($this->actionFactoryMock, 'create')->willReturn($actionInstance);

        // Test
        $this->assertNull($this->model->match($this->requestMock));
    }

    public function testMatchEmptyActionInstance()
    {
        // Test Data
        $nullActionInstance = null;
        $moduleFrontName = 'module front name';
        $actionPath = 'action path';
        $actionName = 'action name';
        $actionClassName = 'Magento\Cms\Controller\Index\Index';
        $moduleName = 'module name';
        $moduleList = [$moduleName];

        // Stubs
        $this->basicStub($this->requestMock, 'getModuleName')->willReturn($moduleFrontName);
        $this->basicStub($this->routeConfigMock, 'getModulesByFrontName')->willReturn($moduleList);
        $this->basicStub($this->requestMock, 'getControllerName')->willReturn($actionPath);
        $this->basicStub($this->requestMock, 'getActionName')->willReturn($actionName);
        $this->basicStub($this->appStateMock, 'isInstalled')->willReturn(false);
        $this->basicStub($this->actionListMock, 'get')->willReturn($actionClassName);
        $this->basicStub($this->actionFactoryMock, 'create')->willReturn($nullActionInstance);

        // Expectations and Test
        $this->assertNull($this->model->match($this->requestMock));
    }

    public function testGetActionClassName()
    {
        $className = 'name of class';
        $module = 'module';
        $prefix = 'Controller';
        $actionPath = 'action path';
        $this->nameBuilderMock->expects($this->once())
            ->method('buildClassName')
            ->with([$module, $prefix, $actionPath])
            ->willReturn($className);
        $this->assertEquals($className, $this->model->getActionClassName($module, $actionPath));

    }

    /**
     * Generate a stub with an expected usage for the request mock object
     *
     * @param string $method
     * @param string $with
     * @return $this
     */
    private function requestExpects($method, $with)
    {
        $this->requestMock->expects($this->once())
            ->method($method)
            ->with($with);
        return $this;
    }
} 