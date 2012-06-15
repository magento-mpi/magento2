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

    public function setUp()
    {
        $this->_menuMock = $this->getMock('Mage_Backend_Model_Menu');

        $mockItem = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $mockItem->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $mockItem->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $mockItem->expects($this->any())->method('getId')->will($this->returnValue('Mage_Adminhtml::system_acl_roles'));
        $mockItem->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/user_role'));

        $this->_menuMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Adminhtml::system_acl_roles'))
            ->will($this->returnValue($mockItem));

        $this->_model = new Mage_Backend_Model_Url(array(
                'startupMenuItemId' => 'Mage_Adminhtml::system_acl_roles',
                'menu' => $this->_menuMock
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
}
