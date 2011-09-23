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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for payment methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_PaymentMethodsWith3DTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->addParameter('tabName', '');
        $this->addParameter('webSite', '');
        $this->addParameter('storeName', '');
    }

    /**
     * <p>Preconditions</p>
     * <p>Creating products</p>
     *
     * @test
     */
    public function createProducts()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData['general_name'];
    }

    /**
     * <p>Credit Card (saved).</p>
     * <p>Steps:</p>
     * <p>1.Create product for adding to shopping cart.</p>
     * <p>2.Add product to shopping cart and proceed to checkout.</p>
     * <p>3.Checkout as guest.</p>
     * <p>4.Fill billing and shipping addresses with correct data.</p>
     * <p>5.Choose Flat Rate / Fixed shipping method.</p>
     * <p>6.Choose 'Credit Card (saved)' in payment method.</p>
     * <p>7.Fill credit card data with visa information.</p>
     * <p>8.Enter 3D security code.</p>
     * <p>9.Place the order.</p>
     * <p>Expected result:</p>
     * <p>Order is placed successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function savedCC($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('saved_cc_with_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_saved_cc_3d',
                array('general_name' => $productData), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>AuthorizeNet.</p>
     * <p>Steps:</p>
     * <p>1.Create product for adding to shopping cart.</p>
     * <p>2.Add product to shopping cart and proceed to checkout.</p>
     * <p>3.Checkout as guest.</p>
     * <p>4.Fill billing and shipping addresses with correct data.</p>
     * <p>5.Choose Flat Rate / Fixed shipping method.</p>
     * <p>6.Choose 'Credit Card (Authorize.net)' in payment method.</p>
     * <p>7.Fill credit card data with visa information.</p>
     * <p>8.Enter 3D security code.</p>
     * <p>9.Place the order.</p>
     * <p>Expected result:</p>
     * <p>Order is placed successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function authorizeNet($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('authorize_net_with_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_authorize_net_3d',
                array('general_name' => $productData), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>PayPal Direct Payment Payflow Edition.</p>
     * <p>Steps:</p>
     * <p>1.Create product for adding to shopping cart.</p>
     * <p>2.Add product to shopping cart and proceed to checkout.</p>
     * <p>3.Checkout as guest.</p>
     * <p>4.Fill billing and shipping addresses with correct data.</p>
     * <p>5.Choose Flat Rate / Fixed shipping method.</p>
     * <p>6.Choose 'PayPal Direct Payment Payflow Edition' in payment method.</p>
     * <p>7.Fill credit card data with visa information.</p>
     * <p>8.Enter 3D security code.</p>
     * <p>9.Place the order.</p>
     * <p>Expected result:</p>
     * <p>Order is placed successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function payPalUKDirect($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('paypal_uk_direct_with_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_paypaluk_direct_3d',
                array('general_name' => $productData), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }
    /**
     * <p>Payflow Pro.</p>
     * <p>Steps:</p>
     * <p>1.Create product for adding to shopping cart.</p>
     * <p>2.Add product to shopping cart and proceed to checkout.</p>
     * <p>3.Checkout as guest.</p>
     * <p>4.Fill billing and shipping addresses with correct data.</p>
     * <p>5.Choose Flat Rate / Fixed shipping method.</p>
     * <p>6.Choose 'Payflow Pro' in payment method.</p>
     * <p>7.Fill credit card data with visa information.</p>
     * <p>8.Enter 3D security code.</p>
     * <p>9.Place the order.</p>
     * <p>Expected result:</p>
     * <p>Order is placed successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function payFlowProVerisign($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('payflow_pro_with_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_payflow_pro_verisign_3d',
                array('general_name' => $productData), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>PayPal Direct Payment.</p>
     * <p>Steps:</p>
     * <p>1.Create product for adding to shopping cart.</p>
     * <p>2.Add product to shopping cart and proceed to checkout.</p>
     * <p>3.Checkout as guest.</p>
     * <p>4.Fill billing and shipping addresses with correct data.</p>
     * <p>5.Choose Flat Rate / Fixed shipping method.</p>
     * <p>6.Choose 'PayPal Direct Payment' in payment method.</p>
     * <p>7.Fill credit card data with visa information.</p>
     * <p>8.Enter 3D security code.</p>
     * <p>9.Place the order.</p>
     * <p>Expected result:</p>
     * <p>Order is placed successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function payPalDirect($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('website_payments_pro_with_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_paypal_direct_payment_3d',
                array('general_name' => $productData), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /*
     * <p>Postconditions</p>
     * <p>Disabling 3D secure for payment methods</p>
     *
     * @test
     */
    public function turnOff3D ()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('all_payment_methods_without_3d');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
    }
}
