<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Captcha\Model;

/**
 * Test captcha observer behavior
 *
 * @magentoAppArea adminhtml
 */
class ObserverTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * @magentoConfigFixture admin_store admin/captcha/forms backend_login
     * @magentoConfigFixture admin_store admin/captcha/enable 1
     * @magentoConfigFixture admin_store admin/captcha/mode always
     */
    public function testBackendLoginActionWithInvalidCaptchaReturnsError()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Url')
            ->turnOffSecretKey();

        $post = array(
            'login' => array(
                'username' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
                'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
            ),
            'captcha' => array(
                'backend_login' => 'some_unrealistic_captcha_value',
            )
        );
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin');
        $this->assertContains(__('Incorrect CAPTCHA'), $this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture admin_store admin/captcha/enable 1
     * @magentoConfigFixture admin_store admin/captcha/forms backend_login
     * @magentoConfigFixture admin_store admin/captcha/mode after_fail
     * @magentoConfigFixture admin_store admin/captcha/failed_attempts_login 1
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCaptchaIsRequiredAfterFailedLoginAttempts()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->setCurrentStore(0);
        $captchaModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Captcha\Helper\Data')
            ->getCaptcha('backend_login');

        try {
            $authModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Auth');
            $authModel->login(
                \Magento\TestFramework\Bootstrap::ADMIN_NAME,
                'wrong_password'
            );
        }
        catch (\Exception $e) {
        }

        $this->assertTrue($captchaModel->isRequired());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Captcha/_files/dummy_user.php
     * @magentoConfigFixture admin_store admin/captcha/enable 1
     * @magentoConfigFixture admin_store admin/captcha/forms backend_forgotpassword
     * @magentoConfigFixture admin_store admin/captcha/mode always
     */
    public function testCheckUserForgotPasswordBackendWhenCaptchaFailed()
    {
        $this->getRequest()->setPost(array(
            'email' => 'dummy@dummy.com',
            'captcha' => array('backend_forgotpassword' => 'dummy')
        ));
        $this->dispatch('backend/admin/auth/forgotpassword');
        $this->assertRedirect($this->stringContains('backend/admin/auth/forgotpassword'));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture admin_store admin/captcha/enable 1
     * @magentoConfigFixture admin_store admin/captcha/forms backend_forgotpassword
     * @magentoConfigFixture admin_store admin/captcha/mode always
     */
    public function testCheckUnsuccessfulMessageWhenCaptchaFailed()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Url')
            ->turnOffSecretKey();
        $this->getRequest()->setPost(array('email'   => 'dummy@dummy.com', 'captcha' => '1234'));
        $this->dispatch('backend/admin/auth/forgotpassword');
        $this->assertSessionMessages(
            $this->equalTo(array('Incorrect CAPTCHA')), \Magento\Core\Model\Message::ERROR,
            'Magento\Backend\Model\Session'
        );
    }
}
