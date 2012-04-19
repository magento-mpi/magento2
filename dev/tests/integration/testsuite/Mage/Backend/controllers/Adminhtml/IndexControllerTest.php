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

    protected  function _login()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $this->_session = Mage::getSingleton('Mage_Admin_Model_Session');
        $this->_session->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    protected  function _logout()
    {
        $this->_session->logout();
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

    /**
     * Test form existace
     * @covers Mage_Backend_Adminhtml_IndexController::forgotpasswordAction
     */
    public function testFormForgotpasswordAction()
    {
        $this->dispatch('admin/index/forgotpassword');
        $expected = 'Forgot your user name or password?';
        $this->assertContains($expected, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_Backend_Adminhtml_IndexController::forgotpasswordAction
     */
    public function testForgotpasswordAction()
    {
        $this->getRequest()->setPost('email', 'test@test.com');
        $this->dispatch('admin/index/forgotpassword');
        $this->assertRedirect();
    }

    /**
     * @covers Mage_Backend_Adminhtml_IndexController::resetPasswordAction
     */
    public function testResetPasswordAction()
    {
//        $this->getRequest()->setParam('token', 'dummy')->setParam('id', 1);
//        $this->dispatch('admin/index/resetPassword');
//        $this->assertRedirect();
    }
}
