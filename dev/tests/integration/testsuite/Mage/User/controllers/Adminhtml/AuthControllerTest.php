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
 * Test class for Mage_User_Adminhtml_AuthController.
 *
 * @group module:Mage_User
 */
class Mage_User_Adminhtml_AuthControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    protected  function _login()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    protected  function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * Test form existence
     * @covers Mage_User_Adminhtml_AuthController::forgotpasswordAction
     */
    public function testFormForgotpasswordAction()
    {
        $this->dispatch('admin/auth/forgotpassword');
        $expected = 'Forgot your user name or password?';
        $this->assertContains($expected, $this->getResponse()->getBody());
    }

    /**
     * Test redirection to startup page after success password recovering posting
     *
     * @covers Mage_User_Adminhtml_AuthController::forgotpasswordAction
     */
    public function testForgotpasswordAction()
    {
        $this->getRequest()->setPost('email', 'test@test.com');
        $this->dispatch('admin/auth/forgotpassword');
        $this->assertRedirect(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl());
    }

    /**
     * Test redirection with posting of incorrect token
     *
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     * @magentoDataFixture emptyDataFixture
     */
    public function testResetPasswordRedirectionWithIncorrectTokenAction()
    {
        $this->_login();
        $this->getRequest()->setParam('token', 'dummy')->setParam('id', 1);
        $this->dispatch('admin/auth/resetPassword');
        $this->assertRedirect();
        $this->_logout();
    }

    /**
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordPostAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     * @magentoDataFixture emptyDataFixture
     */
    public function testResetPasswordPostAction()
    {
        $this->_login();
        $this->getRequest()->setParam('token', 'dummy')->setParam('id', 1);
        $this->dispatch('admin/auth/resetPasswordPost');
        $this->assertRedirect(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl());
        $this->_logout();
    }

    /**
     * Empty data fixture to provide support of transaction
     * @static
     *
     */
    public static function emptyDataFixture()
    {

    }
}
