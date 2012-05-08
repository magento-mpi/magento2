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

/**
 * Test class for Mage_Backend_Adminhtml_IndexController.
 *
 * @group module:Mage_Backend
 */
class Mage_Backend_Adminhtml_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->_session = $this->_auth->getAuthStorage();
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * Check not logged state
     * @covers Mage_Backend_Adminhtml_IndexController::loginAction
     */
    public function testNotLoggedLoginAction()
    {
        $this->dispatch('admin/index/login');
        $this->assertFalse($this->getResponse()->isRedirect());
        $expected = 'Log in to Admin Panel';
        $this->assertContains($expected, $this->getResponse()->getBody(), 'There is no login form');
    }

    /**
     * Check logged state
     * @covers Mage_Backend_Adminhtml_IndexController::loginAction
     */
    public function testLoggedLoginAction()
    {
        $this->_login();
        $this->dispatch('admin/index/login');
        $this->assertRedirect();
        $this->_logout();
    }

    /**
     * @covers Mage_Backend_Adminhtml_IndexController::logoutAction
     */
    public function testLogoutAction()
    {
        $this->_login();
        $this->dispatch('admin/index/logout');
        $this->assertRedirect();
        $this->assertFalse($this->_session->isLoggedIn(), 'User is not logouted');
    }

    /**
     * @covers Mage_Backend_Adminhtml_IndexController::deniedJsonAction
     * @covers Mage_Backend_Adminhtml_IndexController::_getDeniedJson
     */
    public function testDeniedJsonAction()
    {
        $this->_login();
        $this->dispatch('admin/index/deniedJson');
        $expected = '{"ajaxExpired":1,"ajaxRedirect":"http';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * @covers Mage_Backend_Adminhtml_IndexController::deniedIframeAction
     * @covers Mage_Backend_Adminhtml_IndexController::_getDeniedIframe
     */
    public function testDeniedIframeAction()
    {
        $this->_login();
        $this->dispatch('admin/index/deniedIframe');
        $expected = '<script type="text/javascript">parent.window.location =';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->_logout();
    }
}
