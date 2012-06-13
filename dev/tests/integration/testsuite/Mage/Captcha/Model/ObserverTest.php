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

/**
 * Test captcha observer behavior
 */
class Mage_Captcha_Model_ObserverTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoConfigFixture admin_store admin/captcha/forms backend_login
     * @magentoConfigFixture admin_store admin/captcha/enable 1
     * @magentoConfigFixture admin_store admin/captcha/mode always
     */
    public function testBackendLoginActionWithInvalidCaptchaReturnsError()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $post = array(
            'login' => array(
                'username' => Magento_Test_Bootstrap::ADMIN_NAME,
                'password' => Magento_Test_Bootstrap::ADMIN_PASSWORD
            ),
            'captcha' => array(
                'backend_login' => 'some_unrealistic_captcha_value',
            )
        );
        $this->getRequest()->setPost($post);
        $this->dispatch('/admin');
        $this->assertContains(Mage::helper('Mage_Captcha')->__('Incorrect CAPTCHA.'), $this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture current_store admin/captcha/enable 1
     * @magentoConfigFixture current_store admin/captcha/forms backend_login
     * @magentoConfigFixture current_store admin/captcha/mode after_fail
     * @magentoConfigFixture current_store admin/captcha/failed_attempts_login 1
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCaptchaIsRequiredAfterFailedLoginAttempts()
    {
        Mage::app()->setCurrentStore(0);
        $captchaModel = Mage::helper('Mage_Captcha_Helper_Data')->getCaptcha('backend_login');

        try {
            $authModel = new Mage_Backend_Model_Auth();
            $authModel->login(
                Magento_Test_Bootstrap::ADMIN_NAME,
                'wrong_password'
            );
        }
        catch (Exception $e) {
        }

        $this->assertTrue($captchaModel->isRequired());
    }
}
