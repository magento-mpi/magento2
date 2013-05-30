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
class Saas_Mage_Various_CheckoutLinkVerificationTest extends Core_Mage_Various_CheckoutLinkVerificationTest
{
    /**
     * <p>Creating product with required fields only and customer</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTest()
    {
        return parent::preconditionsForTest();
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
        $this->clickControl(self::FIELD_TYPE_LINK, 'my_cart', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'mini_cart');
        $this->clickControl(self::FIELD_TYPE_LINK, 'checkout');
        $this->validatePage('onepage_checkout');
        //Steps
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($product);
        $this->productHelper()->frontAddProductToCart();
        //Validation
        $this->clickControl(self::FIELD_TYPE_LINK, 'my_cart', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'mini_cart');
        $this->clickControl(self::FIELD_TYPE_LINK, 'checkout');
        $this->validatePage('onepage_checkout');
    }
}
