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
 * Tests for shipping methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_Existing_ShippingMethodsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->addParameter('tabName', '');
        $this->addParameter('webSite', '');
        $this->addParameter('storeName', '');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
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
     * Create customer
     *
     * @test
     */
    public function createCustomer()
    {
        //Preconditions
        $userData = $this->loadData('generic_customer_account', null, 'email');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        $this->CustomerHelper()->createCustomer($userData);
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        return $userData;
    }
    /**
     * <p>Shipping Methods.</p>
     * <p>Steps:</p>
     * <p>1.Create product for adding to shopping cart.</p>
     * <p>2.Add product to shopping cart and proceed to checkout.</p>
     * <p>3.Checkout as logged in customer.</p>
     * <p>4.Fill billing and shipping addresses with correct data.</p>
     * <p>5.Choose shipping method (data provider).</p>
     * <p>6.Choose Payment method 'Credit Card (saved)' .</p>
     * <p>7.Fill credit card data with visa information.</p>
     * <p>8.Place the order.</p>
     * <p>Expected result:</p>
     * <p>Order is placed successfully</p>
     *
     * @depends createProducts
     * @depends createCustomer
     * @dataProvider dataShippingMethods
     * @test
     */
    public function shippingMethods($shipping, $productData, $customerData)
    {

        //Data
        $checkoutData = $this->loadData('checkout_data_saved_cc_registered',
                array('general_name' => $productData, 'email_address' => $customerData['email'],
                    'password' => $customerData['password']));
        $checkoutData['shipping_data'] = $this->loadData('front_shipping_' . $shipping);
        //Steps
        $this->systemConfigurationHelper()->configure($shipping . '_enable');
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verifying
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    public function dataShippingMethods()
    {
        return array(
            array('flatrate'),
            array('free'),
            array('ups'),
            array('upsxml'),
            array('usps'),
            array('fedex'),
            array('dhl')
        );
    }
}
