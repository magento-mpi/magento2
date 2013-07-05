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
class Core_Mage_Customer_RedirectAfterLoginTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
        $this->loginAdminUser();
    }

    /**
     * <p>PreConditions for Redirect After Login Test</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $usersData = array(
            $this->loadDataSet('Customers', 'customer_account_register'),
            $this->loadDataSet('Customers', 'customer_account_register')
        );

        //create Simple Product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Register customers
        $this->frontend();
        foreach ($usersData as $user) {
            $this->frontend('customer_login');
            $this->customerHelper()->registerCustomer($user);
            $this->assertMessagePresent('success', 'success_registration');
            $this->logoutCustomer();
        }

        return array(
            'product' => $productData['general_name'],
            'customer_1' => array('email' => $usersData[0]['email'], 'password' => $usersData[0]['password']),
            'customer_2' => array('email' => $usersData[1]['email'], 'password' => $usersData[1]['password'])
        );
    }

    /**
     * <p>Redirect to page from where the customer logged in </p>
     *
     * @depends preconditionsForTests
     * @param $testData
     * @test
     * @TestlinkId -6162
     */
    public function redirectToPreviousPageAfterLogin($testData)
    {
        //Set System-Configurations-Customer Configurations-Login options-
        //Redirect Customer to Account Dashboard after Logging in to "NO"
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CustomerRedirect/disable_customer_configuration_redirect');
        //Go to frontend as non registered customer
        $this->frontend();
        //Open Product Page created from PreConditions page
        $this->productHelper()->frontOpenProduct($testData['product']);
        //Log in as registered from PreConditions customer
        $this->addParameter('referer', $this->defineParameterFromUrl('referer',
            $this->getControlAttribute(self::FIELD_TYPE_LINK, 'log_in', 'href')));
        $this->clickControl('link', 'log_in');
        $this->fillFieldset($testData['customer_1'], 'log_in_customer');
        $this->clickButton('login');
        //Validate that Product page is opened
        $this->validatePage('product_page');
    }

    /**
     * <p>Redirect to account Dashboard after LogIn </p>
     *
     * @depends preconditionsForTests
     * @param $testData
     * @test
     * @TestlinkId -6161
     */
    public function redirectToAccountDashboardAfterLogin($testData)
    {
        //Set System-Configurations-Customer Configurations-Login options-
        //Redirect Customer to Account Dashboard after Logging in to "Yes"
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CustomerRedirect/enable_customer_configuration_redirect');
        //Go to frontend as non registered customer
        $this->frontend();
        //Open Product page
        $this->productHelper()->frontOpenProduct($testData['product']);
        //Log in as registered from Preconditions customer
        $this->customerHelper()->frontLoginCustomer($testData['customer_1']);
        //Validate that Customer Account Dashboard page is opened
        $this->validatePage('customer_account');
    }

    /**
     * <p>Redirect to account Dashboard after LogIn </p>
     * Cover MAGETWO-2465
     *
     * @depends preconditionsForTests
     * @param $testData
     * @test
     */
    public function redirectAfterAnotherUserLogin($testData)
    {
        //Set System-Configurations-Customer Configurations-Login options-
        //Redirect Customer to Account Dashboard after Logging in to "Yes"
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CustomerRedirect/enable_customer_configuration_redirect');
        //Go to frontend
        $this->frontend();
        //Login as first user
        $this->customerHelper()->frontLoginCustomer($testData['customer_1']);
        //Add product to cart
        $this->productHelper()->frontOpenProduct($testData['product']);
        $this->productHelper()->frontAddProductToCart();
        $this->logoutCustomer();
        //Login as second user
        $this->customerHelper()->frontLoginCustomer($testData['customer_2']);
        //Validate that Customer Account Dashboard page is opened
        $this->validatePage('customer_account');
    }
}