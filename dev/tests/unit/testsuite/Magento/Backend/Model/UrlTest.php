<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\Model\Url
 */
namespace Magento\Backend\Model;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Url
     */
    protected  $_model;

    /**
     * Mock menu model
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuMock;

    protected $_areaFrontName = 'backendArea';

    /**
     * @var \Magento\Core\Model\Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreSessionMock;

    /**
     * @var \Magento\Core\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_storeConfigMock;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_menuConfigMock;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_backendHelperMock;

    /**
     * @var \Magento\Core\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreDataMock;

    /**
     * @var \Magento\Core\Controller\Request\Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    public function setUp()
    {
        $this->_menuMock = $this->getMock('Magento\Backend\Model\Menu', array(), array(), '', false);

        $this->_menuConfigMock = $this->getMock('Magento\Backend\Model\Menu\Config', array(), array(), '', false);
        $this->_menuConfigMock->expects($this->any())->method('getMenu')->will($this->returnValue($this->_menuMock));

        $this->_coreSessionMock = $this->getMock('Magento\Core\Model\Session', array('getFormKey'), array(), '', false);
        $this->_coreSessionMock->expects($this->any())->method('getFormKey')->will($this->returnValue('salt'));

        $this->_coreHelperMock = $this->getMock('Magento\Core\Helper\Data', array('getHash'), array(), '', false);
        $this->_coreHelperMock->expects($this->any())->method('getHash')->will($this->returnArgument(0));

        $mockItem = $this->getMock('Magento\Backend\Model\Menu\Item', array(), array(), '', false);
        $mockItem->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $mockItem->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $mockItem->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('Magento_Adminhtml::system_acl_roles'));
        $mockItem->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/user_role'));

        $this->_menuMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Magento_Adminhtml::system_acl_roles'))
            ->will($this->returnValue($mockItem));

        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $helperMock->expects($this->any())->method('getAreaFrontName')
            ->will($this->returnValue($this->_areaFrontName));
        $this->_storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $this->_storeConfigMock->expects($this->any())
            ->method('getConfig')
            ->with(\Magento\Backend\Model\Url::XML_PATH_STARTUP_MENU_ITEM)
            ->will($this->returnValue('Magento_Adminhtml::system_acl_roles'));

        $this->_coreDataMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);

        $this->_model = new \Magento\Backend\Model\Url(
            $helperMock,
            $this->_coreHelperMock,
            $this->_coreSessionMock,
            $this->_storeConfigMock,
            $this->_menuConfigMock,
            $this->_coreDataMock
        );

        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $this->_model->setRequest($this->_requestMock);
    }

    public function testFindFirstAvailableMenuDenied()
    {
        $user = $this->getMock('Magento\User\Model\User', array(), array(), '', false);
        $user->expects($this->once())
            ->method('setHasAvailableResources')
            ->with($this->equalTo(false));
        $mockSession = $this->getMock('Magento\Backend\Model\Auth\Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );

        $mockSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->_model->setSession($mockSession);

        $this->_menuMock->expects($this->any())
            ->method('getFirstAvailableChild')
            ->will($this->returnValue(null));

        $this->assertEquals('*/*/denied', $this->_model->findFirstAvailableMenu());
    }

    public function testFindFirstAvailableMenu()
    {
        $user = $this->getMock('Magento\User\Model\User', array(), array(), '', false);
        $mockSession = $this->getMock('Magento\Backend\Model\Auth\Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );

        $mockSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->_model->setSession($mockSession);

        $itemMock = $this->getMock('Magento\Backend\Model\Menu\Item', array(), array(), '', false);
        $itemMock->expects($this->once())->method('getAction')->will($this->returnValue('adminhtml/user'));
        $this->_menuMock->expects($this->any())
            ->method('getFirstAvailable')
            ->will($this->returnValue($itemMock));

        $this->assertEquals('adminhtml/user', $this->_model->findFirstAvailableMenu());
    }

    public function testGetStartupPageUrl()
    {
        $this->assertEquals('adminhtml/user_role', (string)$this->_model->getStartupPageUrl());
    }

    public function testGetAreaFrontName()
    {
        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $helperMock->expects($this->once())->method('getAreaFrontName')
            ->will($this->returnValue($this->_areaFrontName));

        $urlModel = new \Magento\Backend\Model\Url(
            $helperMock,
            $this->_coreHelperMock,
            $this->_coreSessionMock,
            $this->_storeConfigMock,
            $this->_menuConfigMock,
            $this->_coreDataMock
        );

        $urlModel->getAreaFrontName();
    }

    public function testGetActionPath()
    {
        $moduleFrontName = 'moduleFrontName';
        $controllerName = 'controllerName';
        $actionName = 'actionName';

        $this->_model->setRouteName($moduleFrontName);
        $this->_model->setRouteFrontName($moduleFrontName);
        $this->_model->setControllerName($controllerName);
        $this->_model->setActionName($actionName);

        $actionPath = $this->_model->getActionPath();

        $this->assertNotEmpty($actionPath);
        $this->assertStringStartsWith($this->_areaFrontName . '/', $actionPath);
        $this->assertStringMatchesFormat($this->_areaFrontName . '/%s/%s/%s', $actionPath);
    }

    public function testGetActionPathWhenAreaFrontNameIsEmpty()
    {
        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $helperMock->expects($this->once())->method('getAreaFrontName')
            ->will($this->returnValue(''));

        $urlModel = new \Magento\Backend\Model\Url(
            $helperMock,
            $this->_coreHelperMock,
            $this->_coreSessionMock,
            $this->_storeConfigMock,
            $this->_menuConfigMock,
            $this->_coreDataMock
        );

        $moduleFrontName = 'moduleFrontName';
        $controllerName = 'controllerName';
        $actionName = 'actionName';

        $urlModel->setRouteName($moduleFrontName);
        $urlModel->setRouteFrontName($moduleFrontName);
        $urlModel->setControllerName($controllerName);
        $urlModel->setActionName($actionName);

        $actionPath = $urlModel->getActionPath();

        $this->assertNotEmpty($actionPath);
        $this->assertStringStartsWith($moduleFrontName . '/', $actionPath);
        $this->assertStringMatchesFormat($moduleFrontName . '/%s/%s', $actionPath);
    }

    /**
     * Check that secret key generation is based on usage of routeName passed as method param
     * Params are not equals
     */
    public function testGetSecretKeyGenerationWithRouteNameAsParamNotEquals()
    {
        $routeName = 'adminhtml';
        $controllerName = 'catalog';
        $actionName = 'index';

        $keyWithRouteName = $this->_model->getSecretKey($routeName, $controllerName, $actionName);
        $keyWithoutRouteName = $this->_model->getSecretKey(null, $controllerName, $actionName);
        $keyDummyRouteName = $this->_model->getSecretKey('dummy', $controllerName, $actionName);

        $this->assertNotEquals($keyWithRouteName, $keyWithoutRouteName);
        $this->assertNotEquals($keyWithRouteName, $keyDummyRouteName);
    }

    /**
     * Check that secret key generation is based on usage of routeName passed as method param
     * Params are equals
     */
    public function testGetSecretKeyGenerationWithRouteNameAsParamEquals()
    {
        $routeName = 'adminhtml';
        $controllerName = 'catalog';
        $actionName = 'index';

        $keyWithRouteName1 = $this->_model->getSecretKey($routeName, $controllerName, $actionName);
        $keyWithRouteName2 = $this->_model->getSecretKey($routeName, $controllerName, $actionName);

        $this->assertEquals($keyWithRouteName1, $keyWithRouteName2);
    }

    /**
     * Check that secret key generation is based on usage of routeName extracted from request
     */
    public function testGetSecretKeyGenerationWithRouteNameInRequest()
    {
        $routeName = 'adminhtml';
        $controllerName = 'catalog';
        $actionName = 'index';

        $keyFromParams = $this->_model->getSecretKey($routeName, $controllerName, $actionName);

        $requestMock = $this->getMock('Magento\Core\Controller\Request\Http',
            array('getRouteName', 'getControllerName', 'getActionName', 'getBeforeForwardInfo'),
            array(),
            '',
            false
        );
        $requestMock->expects($this->exactly(3))->method('getBeforeForwardInfo')->will($this->returnValue(null));
        $requestMock->expects($this->once())->method('getRouteName')->will($this->returnValue($routeName));
        $requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue($controllerName));
        $requestMock->expects($this->once())->method('getActionName')->will($this->returnValue($actionName));
        $this->_model->setRequest($requestMock);

        $keyFromRequest = $this->_model->getSecretKey();
        $this->assertEquals($keyFromParams, $keyFromRequest);
    }

    /**
     * Check that secret key generation is based on usage of routeName extracted from request Forward info
     */
    public function testGetSecretKeyGenerationWithRouteNameInForwardInfo()
    {
        $routeName = 'adminhtml';
        $controllerName = 'catalog';
        $actionName = 'index';

        $keyFromParams = $this->_model->getSecretKey($routeName, $controllerName, $actionName);

        $requestMock = $this->getMock('Magento\Core\Controller\Request\Http',
            array('getBeforeForwardInfo'),
            array(),
            '',
            false
        );

        $requestMock->expects($this->at(0))
            ->method('getBeforeForwardInfo')
            ->with('route_name')
            ->will($this->returnValue('adminhtml'));

        $requestMock->expects($this->at(1))
            ->method('getBeforeForwardInfo')
            ->with('route_name')
            ->will($this->returnValue('adminhtml'));

        $requestMock->expects($this->at(2))
            ->method('getBeforeForwardInfo')
            ->with('controller_name')
            ->will($this->returnValue('catalog'));

        $requestMock->expects($this->at(3))
            ->method('getBeforeForwardInfo')
            ->with('controller_name')
            ->will($this->returnValue('catalog'));

        $requestMock->expects($this->at(4))
            ->method('getBeforeForwardInfo')
            ->with('action_name')
            ->will($this->returnValue('index'));

        $requestMock->expects($this->at(5))
            ->method('getBeforeForwardInfo')
            ->with('action_name')
            ->will($this->returnValue('index'));

        $this->_model->setRequest($requestMock);
        $keyFromRequest = $this->_model->getSecretKey();
        $this->assertEquals($keyFromParams, $keyFromRequest);
    }
}
