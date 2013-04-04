<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log in and Reset password actions with enable captcha
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminUser_CaptchaTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->getCurrentPage() != $this->pageAfterAdminLogin) {
            if ($this->controlIsPresent('field', 'captcha')) {
                $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                    'password' => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => 1111);
                $this->adminUserHelper()->loginAdmin($loginData);
                $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getMessagesOnPage());
            } else {
                $this->loginAdminUser();
            }
        }
        $this->navigate('system_configuration');
        $parameters = $this->fixtureDataToArray('Captcha/enable_admin_captcha');
        if (isset($parameters['configuration_scope'])) {
            $this->selectStoreScope('dropdown', 'current_configuration_scope', $parameters['configuration_scope']);
        }
        foreach ($parameters as $value) {
            if (!is_array($value)) {
                continue;
            }
            $settings = (isset($value['configuration'])) ? $value['configuration'] : array();
            if (!empty($value['tab_name'])) {
                $this->systemConfigurationHelper()->openConfigurationTab($value['tab_name']);
                foreach ($settings as $fieldsetName => $fieldsetData) {
                    $this->systemConfigurationHelper()->expandFieldSet($fieldsetName);
                    $this->systemConfigurationHelper()->fillFieldset($fieldsetData, $fieldsetName);
                }
                $this->clickButton('save_config');
            }
        }
    }

    public function tearDownAfterTestClass()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->getCurrentPage() != $this->pageAfterAdminLogin) {
            if ($this->controlIsPresent('field', 'captcha')) {
                $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                                   'password'  => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => 1111);
                $this->adminUserHelper()->loginAdmin($loginData);
                $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getMessagesOnPage());
            } else {
                $this->loginAdminUser();
            }
        }
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_admin_captcha');
    }

    /**
     *  <p>Preconditions:</p>
     * <p>Navigate to Login Admin Page</p>
     */
    protected function assertPreconditions()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->getCurrentPage() != 'log_in_to_admin' && $this->controlIsPresent('link', 'log_out')) {
            $this->logoutAdminUser();
        }
        $this->validatePage('log_in_to_admin');
    }

    /**
     * <p>Login with empty "Captcha"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5467
     */
    public function loginEmptyCaptcha()
    {
        //data
        $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                           'password'  => $this->getConfigHelper()->getDefaultPassword());
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->addParameter('fieldId', 'captcha');
        //Verifying
        $this->assertMessagePresent('validation', 'empty_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Login with wrong "Captcha"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5469
     */
    public function loginWrongCaptcha()
    {
        //data
        $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                           'password'  => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => '1112');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Login with valid "Captcha"</p>
     *
     * @return array
     *
     * @test
     * @TestlinkId TL-MAGE-5468
     */
    public function loginValidCaptcha()
    {
        //Data
        $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                           'password'  => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => '1111');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getParsedMessages());
        $this->logoutAdminUser();

        return $loginData;
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5471
     */
    public function forgotPasswordEmptyCaptcha()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $emailData = array('email' => $userData['email']);
        //Steps
        $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                           'password'  => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => '1111');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getMessagesOnPage());
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->addParameter('fieldId', 'captcha');
        $this->assertMessagePresent('validation', 'empty_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5473
     */
    public function forgotPasswordWrongCaptcha()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15), 'captcha_field' => '1112');
        //Steps
        $this->adminUserHelper()->forgotPassword($emailData);
        $this->addParameter('captcha_field', $emailData['captcha_field']);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5476
     */
    public function forgotPasswordCorrectCaptcha()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15), 'captcha_field' => '1111');
        //Steps
        $this->adminUserHelper()->forgotPassword($emailData);
        $this->addParameter('captcha_field', $emailData['captcha_field']);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'retrieve_password');
    }
}