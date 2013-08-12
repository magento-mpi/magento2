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
 * Test class for Mage_User_Controller_Adminhtml_Auth.
 *
 * @magentoAppArea adminhtml
 */
class Mage_User_Controller_Adminhtml_AuthTest extends Magento_Backend_Utility_Controller
{
    /**
     * Test form existence
     * @covers Mage_User_Controller_Adminhtml_Auth::forgotpasswordAction
     */
    public function testFormForgotpasswordAction()
    {
        $this->dispatch('backend/admin/auth/forgotpassword');
        $expected = 'Forgot your user name or password?';
        $this->assertContains($expected, $this->getResponse()->getBody());
    }

    /**
     * Test redirection to startup page after success password recovering posting
     *
     * @covers Mage_User_Controller_Adminhtml_Auth::forgotpasswordAction
     */
    public function testForgotpasswordAction()
    {
        $this->getRequest()->setPost('email', 'test@test.com');
        $this->dispatch('backend/admin/auth/forgotpassword');
        $this->assertRedirect($this->equalTo(Mage::helper('Magento_Backend_Helper_Data')->getHomePageUrl()));
    }

    /**
     * Test reset password action
     *
     * @covers Mage_User_Controller_Adminhtml_Auth::resetPasswordAction
     * @covers Mage_User_Controller_Adminhtml_Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordAction()
    {
        /** @var $user Mage_User_Model_User */
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $this->assertNotEmpty($user->getId(), 'Broken fixture');
        $resetPasswordToken = Mage::helper('Mage_User_Helper_Data')->generateResetPasswordLinkToken();
        $user->changeResetPasswordLinkToken($resetPasswordToken);
        $user->save();

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId());
        $this->dispatch('backend/admin/auth/resetpassword');

        $this->assertEquals('adminhtml', $this->getRequest()->getRouteName());
        $this->assertEquals('auth', $this->getRequest()->getControllerName());
        $this->assertEquals('resetpassword', $this->getRequest()->getActionName());

        $this->assertContains($resetPasswordToken, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Controller_Adminhtml_Auth::resetPasswordAction
     * @covers Mage_User_Controller_Adminhtml_Auth::_validateResetPasswordLinkToken
     */
    public function testResetPasswordActionWithDummyToken()
    {
        $this->getRequest()->setQuery('token', 'dummy')->setQuery('id', 1);
        $this->dispatch('backend/admin/auth/resetpassword');
        $this->assertSessionMessages(
            $this->equalTo(array('Your password reset link has expired.')), Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect();
    }

    /**
     * @covers Mage_User_Controller_Adminhtml_Auth::resetPasswordPostAction
     * @covers Mage_User_Controller_Adminhtml_Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordPostAction()
    {
        /** @var $user Mage_User_Model_User */
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $this->assertNotEmpty($user->getId(), 'Broken fixture');
        $resetPasswordToken = Mage::helper('Mage_User_Helper_Data')->generateResetPasswordLinkToken();
        $user->changeResetPasswordLinkToken($resetPasswordToken);
        $user->save();

        $newDummyPassword = 'new_dummy_password2';

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId())
            ->setPost('password', $newDummyPassword)
            ->setPost('confirmation', $newDummyPassword);

        $this->dispatch('backend/admin/auth/resetpasswordpost');

        $this->assertRedirect($this->equalTo(Mage::helper('Magento_Backend_Helper_Data')->getHomePageUrl()));

        /** @var $user Mage_User_Model_User */
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $this->assertTrue(
            Mage::helper('Magento_Core_Helper_Data')->validateHash($newDummyPassword, $user->getPassword())
        );
    }

    /**
     * @covers Mage_User_Controller_Adminhtml_Auth::resetPasswordPostAction
     * @covers Mage_User_Controller_Adminhtml_Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordPostActionWithDummyToken()
    {
        $this->getRequest()->setQuery('token', 'dummy')->setQuery('id', 1);
        $this->dispatch('backend/admin/auth/resetpasswordpost');
        $this->assertSessionMessages(
            $this->equalTo(array('Your password reset link has expired.')), Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->equalTo(Mage::helper('Magento_Backend_Helper_Data')->getHomePageUrl()));
    }

    /**
     * @covers Mage_User_Controller_Adminhtml_Auth::resetPasswordPostAction
     * @covers Mage_User_Controller_Adminhtml_Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordPostActionWithInvalidPassword()
    {
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $resetPasswordToken = null;
        if ($user->getId()) {
            $resetPasswordToken = Mage::helper('Mage_User_Helper_Data')
                ->generateResetPasswordLinkToken();
            $user->changeResetPasswordLinkToken($resetPasswordToken);
            $user->save();
        }

        $newDummyPassword = 'new_dummy_password2';

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId())
            ->setPost('password', $newDummyPassword)
            ->setPost('confirmation', 'invalid');

        $this->dispatch('backend/admin/auth/resetpasswordpost');

        $this->assertSessionMessages(
            $this->equalTo(array('Your password confirmation must match your password.')),
            Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect();
    }
}
