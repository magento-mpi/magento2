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
    * Preconditions:
    *
    * Log in to Backend.
    *
    */
    public function setUpBeforeTests()
    {
        $this->windowMaximize();
        $this->loginAdminUser();
    }
   /**
    *
    * Create products for testing.
    *
    * Navigate to Sales-Orders page.
    *
    */
    protected function assertPreConditions()
    {
        $this->orderHelper()->createProducts('product_to_order1');
        $this->orderHelper()->createProducts('product_to_order2');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), 'Wrong page is opened');
        $this->addParameter('id', '0');
        $this->addParameter('shipMethod', 'Fixed');
    }
   /**
    * Create customer via 'Create order' form (required fields are filled). American Express credit card.
    *
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons).
    *
    * 5.Fill all fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products (select third options for second product).
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Credit Card - American Express'
    *
    * 10. Fill in all required fields.
    *
    * 11.Choose first from 'Get shipping methods and rates'.
    *
    * 12.Submit order.
    *
    * 13.Invoice order.
    *
    * 14. Ship order.
    *
    * Expected result:
    *
    * New customer is created. Order is created for the new customer.
    *
    */
    public function testOrderWithCreditCardAmericanExpress()
    {
        //Data
        $data = $this->loadData('new_customer_order_billing_address_reqfields');
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $this->assertTrue($this->fillForm($email, 'order_form_account'));
        //Add products to order
        $this->clickButton('add_products', FALSE);
        //getting products name from dataset. Adding them to the order
        $fieldsetName = 'select_products_to_add';
        $products = $this->loadData('products');
        foreach ($products as $key => $value){
            $prodToAdd = array($key => $value);
            $this->searchAndChoose($prodToAdd, $fieldsetName);
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'credit_card', FALSE);
        $this->pleaseWait();
        $creditCardData = $this->loadData('american_express');
        $this->fillForm($creditCardData, 'order_payment_method');
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_method', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('view_order'));
    }
  /**
    * Create customer via 'Create order' form (required fields are filled). Visa credit card.
    *
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons) if exists.
    *
    * 5.Fill all fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Credit Card - Visa'
    *
    * 10. Fill in all required fields.
    *
    * 11.Choose first from 'Get shipping methods and rates'.
    *
    * 12.Submit order.
    *
    * 13.Invoice order.
    *
    * 14. Ship order.
    *
    * Expected result:
    *
    * New customer is created. Order is created for the new customer.
    *
    */
    public function testOrderWithCreditCardVisa()
    {
        //Data
        $data = $this->loadData('new_customer_order_billing_address_reqfields');
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $this->assertTrue($this->fillForm($email, 'order_form_account'));
        //Add products to order
        $this->clickButton('add_products', FALSE);
        //getting products id and name from dataset. Adding them to the order
        $fieldsetName = 'select_products_to_add';
        $products = $this->loadData('products');
        foreach ($products as $key => $value){
            $prodToAdd = array($key => $value);
            $this->searchAndChoose($prodToAdd, $fieldsetName);
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'credit_card', FALSE);
        $this->pleaseWait();
        $creditCardData = $this->loadData('visa');
        $this->fillForm($creditCardData, 'order_payment_method');
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_method', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('view_order'));
    }
  /**
    * Create customer via 'Create order' form (required fields are filled). MasterCard credit card.
    *
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons) if exists.
    *
    * 5.Fill all fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Credit Card - MasterCard'
    *
    * 10. Fill in all required fields.
    *
    * 11.Choose first from 'Get shipping methods and rates'.
    *
    * 12.Submit order.
    *
    * 13.Invoice order.
    *
    * 14. Ship order.
    *
    * Expected result:
    *
    * New customer is created. Order is created for the new customer.
    *
    */
    public function testOrderWithCreditCardMastercard()
    {
        //Data
        $data = $this->loadData('new_customer_order_billing_address_reqfields');
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $this->assertTrue($this->fillForm($email, 'order_form_account'));
        //Add products to order
        $this->clickButton('add_products', FALSE);
        //getting products id and name from dataset. Adding them to the order
        $fieldsetName = 'select_products_to_add';
        $products = $this->loadData('products');
        foreach ($products as $key => $value){
            $prodToAdd = array($key => $value);
            $this->searchAndChoose($prodToAdd, $fieldsetName);
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'credit_card', FALSE);
        $this->pleaseWait();
        $creditCardData = $this->loadData('mastercard');
        $this->fillForm($creditCardData, 'order_payment_method');
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_method', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('view_order'));
    }
  /**
    * Create customer via 'Create order' form (required fields are filled). Discover credit card.
    *
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons).
    *
    * 5.Fill all fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products (select third options for second product).
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Credit Card - Discover'
    *
    * 10. Fill in all required fields.
    *
    * 11.Choose first from 'Get shipping methods and rates'.
    *
    * 12.Submit order.
    *
    * 13.Invoice order.
    *
    * 14. Ship order.
    *
    * Expected result:
    *
    * New customer is created. Order is created for the new customer.
    *
    */
    public function testOrderWithCreditCardDiscover()
    {
        //Data
        $data = $this->loadData('new_customer_order_billing_address_reqfields');
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $this->assertTrue($this->fillForm($email, 'order_form_account'));
        //Add products to order
        $this->clickButton('add_products', FALSE);
        //getting products id and name from dataset. Adding them to the order
        $fieldsetName = 'select_products_to_add';
        $products = $this->loadData('products');
        foreach ($products as $key => $value){
            $prodToAdd = array($key => $value);
            $this->searchAndChoose($prodToAdd, $fieldsetName);
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'credit_card', FALSE);
        $this->pleaseWait();
        $creditCardData = $this->loadData('discover');
        $this->fillForm($creditCardData, 'order_payment_method');
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_method', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('view_order'));
    }
}
