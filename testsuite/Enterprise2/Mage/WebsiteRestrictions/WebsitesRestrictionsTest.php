<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_WebsiteRestrictionsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Configuration </p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTestClass()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'disable_website_restrictions');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
    }
    /**
     * <p>Check Configuration Fields</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Check that all dropdowns are present:Access Restriction, Restriction Mode, Startup Page, Landing Page, HTTP Response </p>
     * <p>Expected result</p>
     * <p>All fields are present</p>
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
     *
     * <p>Website Closed HTTP Response 200 OK</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Website Closed" in "Restriction Mode"</p>
     * <p>5.Select "503 Service Unavailable" in "Landing Page"</p>
     * <p>6.Select "200 OK" in "HTTP Response"</p>
     * <p>7.Save Config</p>
     * <p>8.Open Frontend Home Page</p>
     * <p>Expected result</p>
     * <p>"503 Service Unavailable" page is open</p>
     * <p>HTTP Responce is "200 OK"</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5520
     */

    public function websiteClosedHttpResponse200()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'website_closed_response_200');
        //Preconditions
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHttpCode('home_page', '200');
        $this->assertEquals($this->getTitle(), '503 Service Unavailable', "Open wrong page");
    }

    /**
     *
     * <p>Website Closed HTTP Response 503</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Website Closed" in "Restriction Mode"</p>
     * <p>5.Select "503 Service Unavailable" in "Landing Page"</p>
     * <p>6.Select "503 Service Unavailable" in "HTTP Response"</p>
     * <p>7.Open Frontend Home Page</p>
     * <p>Expected result</p>
     * <p>"503 Service Unavailable" page is open</p>
     * <p>HTTP Responce is "503 Service Unavailable"</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5521
     */

    public function websiteClosedHttpResponse503()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'website_closed_response_503');
        //Preconditions
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHttpCode('home_page', '503');
        $this->assertEquals($this->getTitle(), '503 Service Unavailable', "Open wrong page");
    }

    /**
     * <p>Redirect to login form in "Login Only" Mode</p>
     * <p>Preconditions:</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Open Frontend</p>
     * <p>Expected result</p>
     * <p>Login page is open</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5525
     */
    public function redirectToLoginFormInLoginOnlyMode()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        //Verification
        $this->validatePage('customer_login');
    }

    /**
     * <p>Redirect to landing page in "Login Only" Mode</p>
     * <p>Preconditions:</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To landing page" in "Startup Page"</p>
     * <p>6.Select "About Us" in "Landing Page"
     * <p>7.Save config</p>
     * <p>8.Open Frontend</p>
     * <p>Expected result</p>
     * <p>About Us page is open</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5526
     */
    public function redirectToLandingPageInLoginOnlyMode()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_landing_page');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        //Verification
        $pagetitle = $this->getTitle();
        $this->assertTrue($pagetitle == 'About Us', "Open wrong page '$pagetitle'");
    }

    /**
     * <p>Verify that "Forgot Your Password" page is enable in "Login Only" Mode</p>
     * <p>Preconditions:</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Open Frontend</p>
     * <p>7.Click "Forgot Your Password" link</p>
     * <p>Expected result</p>
     * <p>Forgot Your Password page is open</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5527
     */
    public function forgotYourPasswordInLoginOnlyMode()
    {
        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->clickControl('link','forgot_password');
        //Verification
        $this->validatePage('forgot_customer_password');
    }

    /**
     * <p>Checkout in "Login Only" Mode</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created</p>
     * <p>2.Customer is created</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Login to Frontend</p>
     * <p>8.Add product to Shopping Cart</p>
     * <p>9.Place order</p>
     * <p>Expected result</p>
     * <p>Order is successfully created</p>
     *
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5522
     */
    public function checkoutInRestrictedMode()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        $user = array('email'    => $userData['email'], 'password' => $userData['password']);
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney',
            array('general_name'  => $simple['general_name'], 'email_address'  => $user['email']));
        //Preconditions
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->fillFieldset($user, 'log_in_customer');
        $this->clickButton('login');
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        //Postcondition
        $this->clickControl('link', 'log_out');

    }

    /**
     * <p>Register customer in "Login and Register" mode </p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login and Register" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Open Frontend</p>
     * <p>8.Click "Register" button</p>
     * <p>9.Fill all required field</p>
     * <p>9.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Customer is registered</p>
     * <p>Open customer Dashboard page</p>
     * <p>Show message "Thank you for registering with store name"</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5523
     */
    public function registerCustomerInLoginAndRegisterMode()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_and_register_to_login_form');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
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
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Open Frontend</p>
     * <p>8.Try to open "Register" page entering URL in browser www.your_site/customer/account/create/</p>
     * <p>Expected result</p>
     * <p>Open Login Page </p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5524
     */
    public function registerCustomerInLoginOnlyMode()
    {

        //Data
        $config = $this->loadDataSet('WebsiteRestrictions', 'login_only_to_login_form');
        //Precondition
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('register_account', false);
        //Verifying
        $this->validatePage('customer_login');
        $this->assertFalse($this->controlIsPresent('button', 'create_account'));
    }
}


