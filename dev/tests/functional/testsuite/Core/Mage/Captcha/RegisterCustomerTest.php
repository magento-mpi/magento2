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
 * Captcha tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_RegisterCustomerTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    public function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    /**
     * <p>Enable Captcha on Register Customer page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2622
     */
    public function enableCaptcha()
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/enable_front_register_captcha');
        $this->frontend('register_account');
        //Validations
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }

    /**
     * <p>Correct CAPTCHA in Register Customer page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5668
     */
    public function correctCaptcha()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $user['captcha'] = '1111';
        //Steps
        $this->frontend('register_account');
        $this->fillFieldset($user, 'account_info');
        $this->clickButton('submit');
        //Verification
        $this->validatePage('customer_account');
    }

    /**
     * <p>Wrong CAPTCHA in Register Customer page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5669
     */
    public function wrongCaptcha()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $user['captcha'] = '1234';
        //Steps
        $this->frontend('register_account');
        $this->fillFieldset($user, 'account_info');
        $this->clickButton('submit');
        //Verification
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Empty CAPTCHA in Register Customer page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5670
     */
    public function emptyCaptcha()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->frontend('register_account');
        $this->fillFieldset($user, 'account_info');
        $this->clickButton('submit', false);
        //Verification
        $this->addFieldIdToMessage('field', 'captcha');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Refreshing CAPTCHA image in Register Customer page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-3789
     */
    public function refreshCaptcha()
    {
        //Steps
        $this->frontend('register_account');
        $captchaUrl1 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }
}