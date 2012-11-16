<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PriceRules
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Applying Shopping Cart Price Rules tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_PriceRules_ShoppingCart_ApplyTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Tax/default_tax_config');
        $this->systemConfigurationHelper()->configure('ShippingSettings/shipping_settings_default');
        $this->systemConfigurationHelper()->configure('Currency/enable_usd');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        $user = $this->loadDataSet('PriceReview', 'customer_account_for_prices_validation');
        $address = $this->loadDataSet('PriceReview', 'customer_account_address_for_prices_validation');
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $categoryPath = $category['parent_category'] . '/' . $category['name'];
        $products = array();
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($user, $address);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Steps
        $this->navigate('manage_products');
        for ($i = 1; $i <= 3; $i++) {
            $simple = $this->loadDataSet('PriceReview', 'simple_product_for_prices_validation_front_' . $i,
                array('categories' => $categoryPath));
            $this->productHelper()->createProduct($simple);
            $this->assertMessagePresent('success', 'success_saved_product');
            $products['sku'][$i] = $simple['general_sku'];
            $products['name'][$i] = $simple['general_name'];
        }
        return array(array('email'    => $user['email'],
                           'password' => $user['password']), $products, $categoryPath);
    }


}
