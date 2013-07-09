<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Captcha
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
class Core_Mage_Captcha_DisplayingSettingsTest extends Mage_Selenium_TestCase
{
    public static $captcha = '1111';

    public function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
        $loginData = array(
            'user_name' => $this->getConfigHelper()->getDefaultLogin(),
            'password' => $this->getConfigHelper()->getDefaultPassword()
        );
        if ($this->controlIsPresent('field', 'captcha')) {
            $loginData['captcha'] = self::$captcha;
        }
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getMessagesOnPage());
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_admin_captcha');
    }

    public function tearDownAfterTest()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
    }

    public function tearDownAfterTestClass()
    {
        $this->assertPreConditions();
    }

    /**
     * <p>Displaying Mode set to Always for Login form</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2722
     */
    public function alwaysModeSet()
    {
        $this->systemConfigurationHelper()->configure('Captcha/choose_login_form');
        self::$captcha = '1111';
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field form on the page');
    }

    /**
     * <p>CAPTCHA on the Login form is always available, If 0 is specified</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2725
     */
    public function withZeroNumberUnsuccessfulAttempts()
    {
        $this->systemConfigurationHelper()->configure('Captcha/zero_attempts_specified');
        self::$captcha = '1111';
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field on the page');
    }

    /**
     * <p>Displaying Mode set to After Number of Attempts for Login form</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2723
     *
     */
    public function noShowingAfterNumberAttemptsModeSet()
    {
        $this->systemConfigurationHelper()->configure('Captcha/admin_captcha_after_attempts_to_login');
        self::$captcha = '1111';
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertFalse($this->controlisVisible('field', 'captcha'), 'There is "Captcha" field on the page');
    }

    /**
     * <p>CAPTCHA showing in Login after specified number</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2724
     */
    public function showingAfterFewAttempts()
    {
        //Data
        $incorrectUser = array('user_name' => $this->generate('text', 10), 'password' => 'password');
        //Steps
        $this->systemConfigurationHelper()->configure('Captcha/admin_captcha_after_attempts_to_login');
        self::$captcha = '1111';
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->adminUserHelper()->loginAdmin($incorrectUser);
        $this->assertFalse($this->controlIsVisible('field', 'captcha'));
        $this->adminUserHelper()->loginAdmin($incorrectUser);
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
    }

    /**
     * <p>Displaying enable "Case sensitive" settings - uppercase</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2743, TL-MAGE-2746
     */
    public function displayingWithEnableCaseSensitive()
    {
        //Data
        $loginData = array(
            'user_name' => $this->getConfigHelper()->getDefaultLogin(),
            'password' => $this->getConfigHelper()->getDefaultPassword(),
            'captcha' => 'aaaa'
        );
        //Steps
        $this->systemConfigurationHelper()->configure('Captcha/case_sensitive_enable');
        self::$captcha = 'AAAA';
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsPresent('field', 'captcha'), 'Message');
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Displaying "Case Sensitive" configuration (disable)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2744, TL-MAGE-2747
     */
    public function displayingDisableCaseSensitive()
    {
        //Data
        $loginData = array(
            'user_name' => $this->getConfigHelper()->getDefaultLogin(),
            'password' => $this->getConfigHelper()->getDefaultPassword(),
            'captcha' => 'aaaa'
        );
        //Steps
        $this->systemConfigurationHelper()->configure('Captcha/case_sensitive_disable');
        self::$captcha = 'aaaa';
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getParsedMessages());
    }
}