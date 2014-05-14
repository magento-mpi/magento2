<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Checkout" link verification - MAGE-5490
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Various_CheckoutLinkVerificationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Creating product with required fields only and customer</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTest()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend();
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            $productData['general_name'],
            array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * <p>"CHECKOUT" link verification on frontend</p>
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
        $this->clickButton('proceed_to_checkout');
        $this->assertTrue($this->checkCurrentPage('onepage_checkout'), $this->getParsedMessages());
        //Steps
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($product);
        $this->productHelper()->frontAddProductToCart();
        //Validation
        $this->clickButton('proceed_to_checkout');
        $this->assertTrue($this->checkCurrentPage('onepage_checkout'), $this->getParsedMessages());
    }
}
