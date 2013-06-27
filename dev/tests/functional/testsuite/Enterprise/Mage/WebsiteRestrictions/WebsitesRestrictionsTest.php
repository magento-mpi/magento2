<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_WebsiteRestrictions
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_WebsiteRestrictions_WebsitesRestrictionsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/disable_website_restrictions');
        $this->clearInvalidedCache();
    }

    /**
     * <p>Check Configuration Fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5519
     */
    public function navigationTest()
    {
        $this->openTab('general_general');
        $this->assertTrue($this->controlIsPresent('dropdown', 'access_restriction'),
            'There is no "access_restriction" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'restriction_mode'),
            'There is no "restriction_mode" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'startup_page'),
            'There is no "startup_page" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'landing_page_restriction'),
            'There is no "landing_page" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'http_response'),
            'There is no "http_response" dropdown on the page');
    }

    /**
     * <p>Checkout in "Login Only" Mode</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5522
     */
    public function checkoutInRestrictedMode()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $loginData = array('email' => $userData['email'], 'password' => $userData['password']);
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney',
            array('general_name' => $simple['general_name']));
        //Preconditions
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/login_only_to_login_form');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->clearInvalidedCache();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->fillFieldset($loginData, 'log_in_customer');
        $this->clickButton('login', false);
        $this->waitForElement(array(
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('link', 'log_out')
        ));
        $this->validatePage();
        $this->assertTrue($this->controlIsPresent('link', 'log_out'), 'Customer is not logged in.');
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        //Postcondition
        $this->clickControl('link', 'log_out');
    }

    /**
     * <p>Website Closed HTTP Response 200 OK</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5520
     */
    public function websiteClosedHttpResponse200()
    {
        //Preconditions
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/website_closed_response_200');
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHttpCode('home_page', '200');
        $this->assertEquals('503 Service Unavailable', $this->title(), "Open wrong page");
    }

    /**
     * <p>Website Closed HTTP Response 503</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5521
     */
    public function websiteClosedHttpResponse503()
    {
        //Preconditions
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/website_closed_response_503');
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHttpCode('home_page', '503');
        $this->assertEquals('503 Service Unavailable', $this->title(), "Open wrong page");
    }

    /**
     * <p>Redirect to login form in "Login Only" Mode</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5525
     */
    public function redirectToLoginFormInLoginOnlyMode()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/login_only_to_login_form');
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        //Verification
        $this->validatePage('customer_login');
    }

    /**
     * <p>Redirect to landing page in "Login Only" Mode</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5526
     */
    public function redirectToLandingPageInLoginOnlyMode()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/login_only_to_landing_page');
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        //Verification
        $this->assertEquals('About Us', $this->title(), "Open wrong page ");
    }

    /**
     * <p>Verify that "Forgot Your Password" page is enable in "Login Only" Mode</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5527
     */
    public function forgotYourPasswordInLoginOnlyMode()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/login_only_to_login_form');
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->clickControl('link', 'forgot_password');
        //Verification
        $this->validatePage('forgot_customer_password');
    }

    /**
     * <p>Register customer in "Login and Register" mode </p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5523
     */
    public function registerCustomerInLoginAndRegisterMode()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/login_and_register_to_login_form');
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->customerHelper()->registerCustomer($user);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        $this->validatePage('customer_account');
        //Postcondition
        $this->clickControl('link', 'log_out');
    }

    /**
     * <p>Register customer in "Login Only" mode </p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5524
     */
    public function registerCustomerInLoginOnlyMode()
    {
        //Precondition
        $this->systemConfigurationHelper()->configure('WebsiteRestrictions/login_only_to_login_form');
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('register_account', false);
        //Verifying
        $this->validatePage('customer_login');
        $this->assertFalse($this->controlIsPresent('button', 'create_account'));
    }
}