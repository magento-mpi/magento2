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
class Community2_Mage_Captcha_DisplayingSettingsTest extends Mage_Selenium_TestCase
{
    public static $captcha = '';

    public function assertPreConditions()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->getCurrentPage() != $this->_firstPageAfterAdminLogin) {
            if ($this->controlIsPresent('field', 'captcha')) {
                $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                                   'password'  => $this->getConfigHelper()->getDefaultPassword(),
                                   'captcha'   => self::$captcha);
                $this->adminUserHelper()->loginAdmin($loginData);
                $this->assertTrue($this->checkCurrentPage('dashboard'), $this->getMessagesOnPage());
            } else {
                $this->loginAdminUser();
            }
        }
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_admin_captcha');
    }

    public function tearDownAfterTestClass()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->getCurrentPage() != $this->_firstPageAfterAdminLogin) {
            if ($this->controlIsPresent('field', 'captcha')) {
                $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                                   'password'  => $this->getConfigHelper()->getDefaultPassword(),
                                   'captcha'   => self::$captcha);
                $this->adminUserHelper()->loginAdmin($loginData);
                $this->assertTrue($this->checkCurrentPage('dashboard'), $this->getMessagesOnPage());
            } else {
                $this->loginAdminUser();
            }
        }
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_admin_captcha');
    }

    /**
     * <p>Displaying Mode set to Always for Login form</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for Login form </p>
     * <p>2. Set Displaying Mode to Always <p/>
     * <p>3.Log out;</p>
     * <p4.Fill all mandatory fields including CAPTCHA with correct data and click Login</p>
     * <p>Expected result:</p>
     * <p>CAPTCHA is present on the Login page"</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2722
     */
    public function alwaysModeSet()
    {
        self::$captcha = '1111';
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/choose_login_form');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field form on the page');
    }

    /**
     * <p>CAPTCHA on the Login form is always available, If 0 is specified</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for Login form </p>
     * <p>2. Set Displaying Mode to After Number of attempts <p/>
     * <p>3.  Number of Unsuccessful Attempts to Login - enter 0</p>
     * <p>4.Log out;</p>
     * <p>Expected result:</p>
     * <p> CAPTCHA on the Login form is always available,if 0 is specified</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2725
     */
    public function withZeroNumberUnsuccessfulAttempts()
    {
        self::$captcha = '1111';
        $this->systemConfigurationHelper()->configure('Captcha/zero_attempts_specified');
        $this->flushCache();
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field on the page');
    }

    /**
     * <p>Displaying Mode set to After Number of Attempts for Login form</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for Login form </p>
     * <p>2. Set Displaying Mode to After Number of attempts <p/>
     * <p>3. Enter a numeric value  in  "Number of Unsuccessful Attempts to Login" field</p>
     * <p>4.Log out;</p>
     * <p>Expected result:</p>
     * <p>CAPTCHA should not presented in "Login" form"</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2723
     *
     */
    public function noShowingAfterNumberAttemptsModeSet()
    {
        self::$captcha = '1111';
        $this->systemConfigurationHelper()->configure('Captcha/admin_captcha_after_attempts_to_login');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertFalse($this->controlisVisible('field', 'captcha'), 'There is "Captcha" field on the page');
    }

    /**
     * <p>CAPTCHA showing in Login after specified number</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for Login form </p>
     * <p>2. Set Displaying Mode to After Number of attempts <p/>
     * <p>3. Enter Number of Unsuccessful Attempts to Login - enter 2 e.g.</p>
     * <p>4.Log out;</p>
     * <p>5.Make three unsuccessful login attempts</p>
     * <p>Expected result:</p>
     * <p>CAPTCHA should be showed only after second unsuccessful attempt to Login"</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2724
     */
    public function showingAfterFewAttempts()
    {
        self::$captcha = '1111';
        //Data
        $incorrectUser = array('user_name' => $this->generate('text', 10), 'password' => 'password');
        $this->systemConfigurationHelper()->configure('Captcha/admin_captcha_after_attempts_to_login');
        //Steps
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->fillFieldset($incorrectUser, 'log_in');
        $this->clickButton('login');
        $this->assertFalse($this->controlIsVisible('field', 'captcha'));
        $this->fillFieldset($incorrectUser, 'log_in');
        $this->clickButton('login');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
    }

    /**
     * <p>Displaying enable "Case sensitive" settings - uppercase</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for all forms </p>
     * <p>2. Type the characters of uppercase into "Symbols used in CAPTCHA" field<p/>
     * <p>3. Select "Yes" into "Case sensitive" field</p>
     * <p>3.Log out;</p>
     * <p>Enter to CAPTCHA the symbols of lowercase</p>
     * <p>Expected result:</p>
     * <p>User should not pass CAPTCHA.  Error message "Incorrect CAPTCHA."  should be displayed."</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2743, TL-MAGE-2746
     */
    public function displayingWithEnableCaseSensitive()
    {
        self::$captcha = 'AAAA';
        $this->systemConfigurationHelper()->configure('Captcha/case_sensitive_enable');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsPresent('field', 'captcha'), 'Message');
        $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                           'password'  => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => 'aaaa');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Displaying "Case Sensitive" configuration (disable)</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for all forms </p>
     * <p>2. Type the characters of uppercase into "Symbols used in CAPTCHA" field<p/>
     * <p>3. Select "No" into "Case sensitive" field</p>
     * <p>3.Log out;</p>
     * <p>Enter to CAPTCHA the symbols of lowercase</p>
     * <p>Expected result:</p>
     * <p> User should  pass CAPTCHA successfully</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2744, TL-MAGE-2747
     */
    public function displayingDisableCaseSensitive()
    {
        self::$captcha = 'aaaa';
        $this->systemConfigurationHelper()->configure('Captcha/case_sensitive_disable');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        if ($this->controlIsPresent('field', 'captcha')) {
            $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                               'password'  => $this->getConfigHelper()->getDefaultPassword(), 'captcha' => 'aaaa');
            $this->adminUserHelper()->loginAdmin($loginData);
            $this->assertTrue($this->checkCurrentPage('dashboard'), $this->getParsedMessages());
        }
    }
}
