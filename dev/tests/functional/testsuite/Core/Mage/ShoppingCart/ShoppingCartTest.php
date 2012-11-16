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
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array( 'user'     => $userData,
                      'product' => array ('name' => $simple['general_name'],
                                          'sku'  => $simple['general_sku']));
    }

    /**
     * <p>Shopping Cart item block contain product</p>
     * <p>Preconditions</p>
     * <p>Customer and product are created</p>
     * <p>Steps</p>
     * <p>1. Login to Frontend</p>
     * <p>2. Add product to Shopping Cart</p>
     * <p>3. Login to Backend</p>
     * <p>4. Open Manage Customer page</p>
     * <p>5. Open Edit Customer page</p>
     * <p>Expected result</p>
     * <p>Shopping Cart item block contain Product</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @test
     * @author Maksym_Iakusha
     * @TestlinkId TL-MAGE-5464
     */
    public function verifyShoppingCartGridOnBackend($testData)
    {
        //Data
        $this->addParameter('websiteId', '1');
        $this->addParameter('productName', $testData['product']['name']);
        $loginData = array('email' => $testData['user']['email'], 'password' => $testData['user']['password']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($loginData);
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
