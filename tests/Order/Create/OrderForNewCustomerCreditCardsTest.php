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
 * Creating order for new customer with one required field empty
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderForNewCustomerCreditCards_Test extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }
    /**
     * @test
     */
    public function createProducts()
    {
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');

        return $productData;
    }
    protected function assertPreConditions()
    {}
    /**
     * <p>Create customer via 'Create order' form (required fields are filled). American Express credit card.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons).</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products (select third options for second product).</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - American Express'</p>
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
    public function orderWithCreditCardAmericanExpress($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'shipping', $symNum = 32, TRUE),
                'american_express','Fixed');
    }
    /**
     * <p>Create customer via 'Create order' form (required fields are filled). Visa credit card.</p>
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
    public function orderWithCreditCardVisa($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'shipping', $symNum = 32, TRUE),
                'visa','Fixed');
    }
    /**
     * <p>Create customer via 'Create order' form (required fields are filled). MasterCard credit card.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - MasterCard'</p>
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
    public function orderWithCreditCardMastercard($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'shipping', $symNum = 32, TRUE),
                'mastercard','Fixed');
    }
    /**
     * <p>Create customer via 'Create order' form (required fields are filled). Discover credit card.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons).</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products (select third options for second product).</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - Discover'</p>
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
    public function orderWithCreditCardDiscover($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'shipping', $symNum = 32, TRUE),
                'discover','Fixed');
    }
}
