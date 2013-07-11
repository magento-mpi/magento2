<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ShoppingCart
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Shopping Cart
 *
 * @package     Mage_ShoppingCart
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ShoppingCart_ShoppingCartTest extends Mage_Selenium_TestCase
{
    /**
     * Create Customer and Product
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'user' => array('email' => $userData['email'], 'password' => $userData['password']),
            'product' => array('name' => $simple['general_name'], 'sku' => $simple['general_sku'])
        );
    }

    /**
     * <p>Shopping Cart item block contain product</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-5464
     */
    public function verifyShoppingCartGridOnBackend($testData)
    {
        //Data
        $this->addParameter('websiteId', '1');
        $this->addParameter('productName', $testData['product']['name']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->productHelper()->frontAddProductToCart();
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $testData['user']['email']));
        $this->clickControl('link', 'view_shopping_cart', false);
        $this->waitForAjax();
        //Verification
        $this->assertTrue($this->controlIsPresent('pageelement', 'products_in_shoping_cart_grid'),
            'Product is absent in Shopping Cart item block');
    }
}
