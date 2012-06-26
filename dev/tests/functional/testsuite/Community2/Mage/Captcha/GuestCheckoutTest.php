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
 * Captcha Guest Checkout tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Captcha_GuestCheckoutTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Captcha', 'disable_frontend_captcha'));
    }

    /**
     * Create product
     * @return string
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
     * <p>Steps:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Checkout as Guest" is selected</p>
     * <p>4.Open Frontend</p>
     * <p>5.Add any product to shopping cart</p>
     * <p>6.Proceed to checkout</p>
     * <p>7.Select "Checkout as Guest" and click "Continue" button</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is present</p>
     * <p>"Please type the letters below" field is present</p>
     * <p>Reload Captcha image is present</p>
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
        $config = $this->loadDataSet('Captcha', 'enable_front_guest_checkout_captcha');
        $method = array('checkout_method' => 'guest');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
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
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Select "Register" and click "Continue" button</p>
     * <p>5.Click "Refresh" capcha image</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is refreshed</p>
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
        $xpath = $this->_getControlXpath('pageelement', 'captcha_guest_checkout') . '@src';
        $captchaUrl1 = $this->getAttribute($xpath);
        $this->clickControl('button', 'captcha_reload_guest_checkout', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getAttribute($xpath);
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }

    /**
     * <p>Empty Captcha for Guest Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Select "Checkout as Guest" and click "Continue" button</p>
     * <p>5.Fill all requirement fields except Captcha </p>
     * <p>6.Click "Continue" button </p>
     * <p>Expected result</p>
     * <p>Show validation message "This is a required field."</p>
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
        $checkoutData =$this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
                                          array('general_name' => $productName));
        $message = '"Please type the letters below": This is a required field.';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
    }

    /**
     * <p>Wrong Captcha for Guest Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Select "Checkout as Guest" and click "Continue" button</p>
     * <p>5.Fill wrong Captcha and all requirement fields</p>
     * <p>6.Click "Continue" button </p>
     * <p>Expected result</p>
     * <p>Show message "Incorrect CAPTCHA."</p>
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
        $checkoutData =$this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
                                            array('general_name' => $productName));
        $checkoutData['billing_address_data']['captcha_guest_checkout'] = '1234';
        $message = 'Incorrect CAPTCHA.';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
    }

    /**
     * <p>Correct Captcha for Guest Checkout</p>
     * <p>Steps:</p>
     * <p>1.Open Frontend</p>
     * <p>2.Add any product to shopping cart</p>
     * <p>3.Proceed to checkout</p>
     * <p>4.Select "Checkout as Guest" and click "Continue" button</p>
     * <p>5.Fill all requirement fields and correct CAPTCHA</p>
     * <p>6.Click "Continue" button </p>
     * <p>Expected result</p>
     * <p>"Shipping Method" tab is opened</p>
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
        $userInfo = $this->loadDataSet('OnePageCheckout', 'billing_guest_req_physical_withshipping');
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
        //Verifing
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
    }
}
