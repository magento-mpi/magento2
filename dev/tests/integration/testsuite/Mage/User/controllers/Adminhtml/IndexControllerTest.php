<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_User_Adminhtml_UserController.
 *
 * @group module:Mage_User
 */
class Mage_User_Adminhtml_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
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
     * Test form existace
     * @covers Mage_User_Adminhtml_UserController::forgotpasswordAction
     */
    public function testFormForgotpasswordAction()
    {
        $this->dispatch('admin/index/forgotpassword');
        $expected = 'Forgot your user name or password?';
        $this->assertContains($expected, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::forgotpasswordAction
     */
    public function testForgotpasswordAction()
    {
        $this->getRequest()->setPost('email', 'test@test.com');
        $this->dispatch('admin/index/forgotpassword');
        $this->assertRedirect();
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::resetPasswordAction
     * @covers Mage_User_Adminhtml_UserController::_validateResetPasswordLinkToken
     */
    public function testResetPasswordAction()
    {
        $this->_login();
        $this->getRequest()->setParam('token', 'dummy')->setParam('id', 1);
        $this->dispatch('admin/index/resetPassword');
        $this->assertRedirect();
        $this->_logout();
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::resetPasswordPostAction
     * @covers Mage_User_Adminhtml_UserController::_validateResetPasswordLinkToken
     */
    public function testResetPasswordPostAction()
    {
        $this->_login();
        $this->getRequest()->setParam('token', 'dummy')->setParam('id', 1);
        $this->dispatch('admin/index/resetPasswordPost');
        $this->assertRedirect();
        $this->_logout();
    }
}
