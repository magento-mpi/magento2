<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Captcha
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha Guest Checkout tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_GuestCheckoutTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    public function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    /**
     * Create product
     *
     * @return string
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simple['general_name'];
    }

    /**
     * <p>Enable Captcha on Guest Checkout page</p>
     *
     * @param string $productName
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2624
     */
    public function enableCaptcha($productName)
    {
        //Data
        $method = array('checkout_method' => 'guest');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/enable_front_guest_checkout_captcha');
        $this->frontend();
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontSelectCheckoutMethod($method);
        //Verification
        $this->assertFalse($this->controlIsVisible('field', 'captcha_user_login'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'captcha_user_login'));
        $this->assertFalse($this->controlIsVisible('button', 'captcha_reload_user_login'));
        $this->assertTrue($this->controlIsVisible('field', 'captcha_guest_checkout'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha_guest_checkout'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload_guest_checkout'));
        $this->assertFalse($this->controlIsVisible('field', 'captcha_register_during_checkout'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'captcha_register_during_checkout'));
        $this->assertFalse($this->controlIsVisible('button', 'captcha_reload_register_during_checkout'));
    }

    /**
     * <p>Reload Captcha for Guest Checkout</p>
     *
     * @param string $productName
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-3792
     */
    public function refreshCaptcha($productName)
    {
        //Data
        $checkout = array('checkout_method' => 'guest');
        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontSelectCheckoutMethod($checkout);
        $captchaUrl1 = $this->getControlAttribute('pageelement', 'captcha_guest_checkout', 'src');
        $this->clickControl('button', 'captcha_reload_guest_checkout', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getControlAttribute('pageelement', 'captcha_guest_checkout', 'src');
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }

    /**
     * <p>Empty Captcha for Guest Checkout</p>
     *
     * @param string $productName
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5657
     */
    public function emptyCaptcha($productName)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney_usa',
            array('general_name' => $productName));
        $message = '"Please enter the letters below": This is a required field.';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
    }

    /**
     * <p>Wrong Captcha for Guest Checkout</p>
     *
     * @param string $productName
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5658
     */
    public function wrongCaptcha($productName)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney_usa',
            array('general_name' => $productName));
        $checkoutData['billing_address_data']['captcha_guest_checkout'] = '1234';
        $message = 'Incorrect CAPTCHA';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
    }

    /**
     * <p>Correct Captcha for Guest Checkout</p>
     *
     * @param string $productName
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5659
     */
    public function correctCaptcha($productName)
    {
        //Data
        $userInfo = $this->loadDataSet('OnePageCheckout', 'billing_guest_withshipping_usa');
        $userInfo['captcha_guest_checkout'] = '1111';
        unset ($userInfo['billing_address_choice']);
        $checkout = array('checkout_method' => 'guest');
        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart();
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontSelectCheckoutMethod($checkout);
        $this->fillFieldset($userInfo, 'billing_information');
        $this->clickButton('billing_information_continue', false);
        $this->waitForAjax();
        //Verifying
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
    }
}