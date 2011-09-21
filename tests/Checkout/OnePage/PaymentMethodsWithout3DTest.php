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
class Checkout_OnePage_PaymentMethodsWithout3DTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'System Configuration' page</p>
     * <p>Enable all payment methods</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->systemConfigurationHelper()->configure('all_payment_methods_without_3d');
    }
    protected function assertPreConditions()
    {}
    /**
     * @test
     */
    public function createProducts()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData['general_name'];
    }

    /**
     * <p>Visa credit card.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function savedCC($productData)
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $checkoutData = $this->loadData('checkout_data_saved_cc',
                array('general_name' => $productData), array('billing_email'));
        $this->checkoutHelper()->frontCreateCheckout($checkoutData);
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>Check/Money order.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Check/Money Order'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function checkMoneyOrder($productData)
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $checkoutData = $this->loadData('checkout_data_check_money_order',
                array('general_name' => $productData), array('billing_email'));
        $this->checkoutHelper()->frontCreateCheckout($checkoutData);
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>AuthorizeNet.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Invoice order.</p>
     * <p>14. Ship order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function authorizeNet($productData)
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $checkoutData = $this->loadData('checkout_data_authorize_net',
                array('general_name' => $productData), array('billing_email'));
        $this->checkoutHelper()->frontCreateCheckout($checkoutData);
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>PayPalUkDirect.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPalUkDirect'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function payPalUKDirect($productData)
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $checkoutData = $this->loadData('checkout_data_paypaluk_direct',
                array('general_name' => $productData), array('billing_email'));
        $this->checkoutHelper()->frontCreateCheckout($checkoutData);
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }
    /**
     * <p>PayflowPro.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Payflow Pro'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function payFlowProVerisign($productData)
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $checkoutData = $this->loadData('checkout_data_payflow_pro_verisign',
                array('general_name' => $productData), array('billing_email'));
        $this->checkoutHelper()->frontCreateCheckout($checkoutData);
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>Website payments pro.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPal Direct'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function payPalDirect($productData)
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $checkoutData = $this->loadData('checkout_data_paypal_direct_payment',
                array('general_name' => $productData), array('billing_email'));
        $this->checkoutHelper()->frontCreateCheckout($checkoutData);
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }
}
