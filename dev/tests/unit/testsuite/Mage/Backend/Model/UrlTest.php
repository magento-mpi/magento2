<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Model_Url
 */
class Mage_Backend_Model_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url
     */
    protected  $_model;

    /**
     * Mock menu model
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuMock;

    protected $_areaFrontName = 'backendArea';

    public function setUp()
    {
        $this->_menuMock = $this->getMock('Mage_Backend_Model_Menu', array(), array(), '', false);

        $mockItem = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $mockItem->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $mockItem->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $mockItem->expects($this->any())->method('getId')->will($this->returnValue('Mage_Adminhtml::system_acl_roles'));
        $mockItem->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/user_role'));

        $this->_menuMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Adminhtml::system_acl_roles'))
            ->will($this->returnValue($mockItem));

        $helperMock = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->any())->method('getAreaFrontName')
            ->will($this->returnValue($this->_areaFrontName));

        $this->_model = new Mage_Backend_Model_Url(array(
                'startupMenuItemId' => 'Mage_Adminhtml::system_acl_roles',
                'menu' => $this->_menuMock,
                'backendHelper' => $helperMock
            )
        );
    }

    public function testFindFirstAvailableMenuDenied()
    {
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $user->expects($this->once())
            ->method('setHasAvailableResources')
            ->with($this->equalTo(false));
        $mockSession = $this->getMock('Mage_Backend_Model_Auth_Session',
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
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $mockSession = $this->getMock('Mage_Backend_Model_Auth_Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );

        $mockSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->_model->setSession($mockSession);

        $itemMock = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $itemMock->expects($this->once())->method('getAction')->will($this->returnValue('adminhtml/user'));
        $this->_menuMock->expects($this->any())
            ->method('getFirstAvailable')
            ->will($this->returnValue($itemMock));

        $this->assertEquals('adminhtml/user', $this->_model->findFirstAvailableMenu());
    }

    public function testGetStartupPageUrl()
    {
        $mockSession = $this->getMock('Mage_Backend_Model_Auth_Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );
        $mockSession->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(true));
        $this->_model->setSession($mockSession);
        $this->assertEquals('adminhtml/user_role', (string)$this->_model->getStartupPageUrl());
    }

    public function testGetAreaFrontName()
    {
        $helperMock = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->once())->method('getAreaFrontName')
            ->will($this->returnValue($this->_areaFrontName));

        $urlModel = new Mage_Backend_Model_Url(array(
            'startupMenuItemId' => 'Mage_Adminhtml::system_acl_roles',
            'menu' => $this->_menuMock,
            'backendHelper' => $helperMock
        ));

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
        $helperMock = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $helperMock->expects($this->once())->method('getAreaFrontName')
            ->will($this->returnValue(''));

        $urlModel = new Mage_Backend_Model_Url(array(
            'startupMenuItemId' => 'Mage_Adminhtml::system_acl_roles',
            'menu' => $this->_menuMock,
            'backendHelper' => $helperMock
        ));

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
}
