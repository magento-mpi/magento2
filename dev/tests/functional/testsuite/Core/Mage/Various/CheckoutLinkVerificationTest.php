<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Various
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Checkout" link verification - MAGE-5490
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Various_CheckoutLinkVerificationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Creating product with required fields only and customer</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTest()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array($productData['general_name'], array('email'    => $userData['email'],
                                                         'password' => $userData['password']));
    }

    /**
     * <p>"CHECKOUT" link verification on frontend</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created;</p>
     * <p>2.Customer without address is created and Logged In;</p>
     * <p>Steps:</p>
     * <p>1. Open product page;</p>
     * <p>2. Add product to Shopping Cart;</p>
     * <p>3. Click on "CHECKOUT" link;</p>
     * <p>4. Log Out Customer;</p>
     * <p>5. Open product page;</p>
     * <p>6. Add product to Shopping Cart;</p>
     * <p>7. Click on "CHECKOUT" link;</p>
     * <p>Expected result:</p>
     * <p>User is redirected to OnePageCheckout after steps 3 and 7;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTest
     */
    public function frontendCheckoutLinkVerification($testData)
    {
        //Data
        list($product, $customer) = $testData;
        //Steps and Verification
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->productHelper()->frontOpenProduct($product);
        $this->productHelper()->frontAddProductToCart();
        $this->clickControl('link', 'checkout');
        $this->validatePage('onepage_checkout');
        //Steps
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($product);
        $this->productHelper()->frontAddProductToCart();
        //Validation
        $this->clickControl('link', 'checkout');
        $this->validatePage('onepage_checkout');
    }
}
