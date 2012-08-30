<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Redirect after Login tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Customer_RedirectAfterLoginTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * PreConditions for Redirect After Login Test
     * @test
     */
    public function preconditionsForTests()
    {
        //Register new customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //create Simple Product
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        return array('email' => $userData['email'], 'password' => $userData['password'],
                     'name'  => $productData['general_name']);
    }

    /**
     * <p>Redirect to account Dashboard after LogIn </p>
     * <p>Preconditions:</p>
     * <p>LogIn to Backend </p>
     * <p>Register new Customer
     * <p>Create simple Product</p>
     * <p>Steps:</p>
     * <p>1.Set System-Configurations-Customer Configurations-Login options- Redirect Customer to Account Dashboard after Logging in to "Yes"
     * <p>2.Go to frontend as non registered customer</p>
     * <p>3.Go to created in PreConditions Product Page</p>
     * <p>4.Log In as a registered Customer</p>
     * <p>Expected result:</p>
     * <p>Customer Account Dashboard page is opened</p>
     *
     * @depends preconditionsForTests
     * @param $userData
     * @ test
     * @TestlinkId
     */
    public function redirectToAccountDashboardAfterLogin($userData)
    {
        //Set System-Configurations-Customer Configurations-Login options- Redirect Customer to Account Dashboard after Logging in to "Yes"
        $this->navigate('system_configuration');
        $redirectOption = $this->loadDataSet('CustomerRedirect', 'customers_customer_configuration_redirect');
        $this->systemConfigurationHelper()->configure($redirectOption);
        //Go to frontend as non registered customer
        $this->frontend();
        //Open Product page
        $this->productHelper()->frontOpenProduct($userData['name']);
        //Log in as registered from Preconditions customer
        $this->customerHelper()->frontLoginCustomer(array('email'    => $userData['email'],
                                                          'password' => $userData['password']));
        //Validate that Customer Account Dashboard page is opened
        $this->validatePage('customer_account');
    }

    /**
     * <p>Redirect to page from where the customer logged in </p>
     * <p>Preconditions:</p>
     * <p>LogIn to Backend </p>
     * <p>Register new Customer
     * <p>Steps:</p>
     * <p>1.Set System-Configurations-Customer Configurations-Login options- Redirect Customer to Account Dashboard after Logging in to "NO"
     * <p>2.Go to frontend as non registered  customer</p>
     * <p>3.Go to created from PreConditions Product Page</p>
     * <p>4.Log In as a registered from PreConditions customer
     * <p>Expected result:</p>
     * <p>Product Page is opened </p>
     *
     * @depends preconditionsForTests
     * @param $userData
     * @test
     * @TestlinkId
     */
    public function redirectToPreviousPageAfterLogin($userData)
    {
        //Set System-Configurations-Customer Configurations-Login options- Redirect Customer to Account Dashboard after Logging in to "NO"
        $this->navigate('system_configuration');
        $redirectOption = $this->loadDataSet('CustomerRedirect', 'customers_customer_configuration_redirect',
            array('redirect_customer_to_account_dashboard_after_logging_in' => 'No'));
        $this->systemConfigurationHelper()->configure($redirectOption);
        //Go to frontend as non registered customer
        $this->frontend();
        //Open Product Page created from PreConditions page
        $this->productHelper()->frontOpenProduct($userData['name']);
        //Log in as registered from PreConditions customer
        if ($this->getCurrentPage() == 'customer_account') {
            $this->clickControl('link', 'log_out', false);
            $this->waitForTextPresent('You are now logged out');
            $this->waitForTextNotPresent('You are now logged out');
            $this->deleteAllVisibleCookies();
            $this->validatePage('home_page');
        }
        $this->customerHelper()->clickControl('link', 'log_in', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('referer', $this->defineParameterFromUrl('referer'));
        $this->validatePage('customer_login_refer');
        $this->fillFieldset(array('email' => $userData['email'], 'password' => $userData['password']),
            'log_in_customer');
        $this->clickButton('login', false);
        //Validate that Product page is opened
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage('product_page');
    }
}