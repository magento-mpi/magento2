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
 * Disable captcha in the Login and Forgot Password forms
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_DisableTest extends Mage_Selenium_TestCase
{
    /**
     * <p>CAPTCHA: Disabled CAPTCHA is not displayed for all forms</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2599, TL-MAGE-2603
     */
    public function captchaDisable()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_admin_captcha');
        $this->logoutAdminUser();
        $this->assertFalse($this->controlIsVisible('field', 'captcha'), 'There is "Captcha" field form on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertFalse($this->controlisVisible('field', 'captcha_field'), 'There is "Captcha" field on the page');
    }
}