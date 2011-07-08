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
 * Creating order for new customer with credit
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderForNewCustomerWithCredit_Test extends Mage_Selenium_TestCase
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

    }
    
    /**
     * Create customer via 'Create order' form (all fields are filled with spec sym).
     * Create order(all fields are filled).
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
     * 9.Check payment method 'Check / Money order'
     * 
     * 10.Choose first from 'Get shipping methods and rates'.
     * 
     * 11.Submit order.
     * 
     * 12.Invoice order.
     * 
     * 13. Ship order.
     *
     * Expected result:
     *
     * New customer successfully created. Order is created for the new customer, invoiced and shipped.
     *
     *
     */    
    public function testOrderCreditSpecialCharacters()
    {
        //Data
        $data = $this->loadData(
                        'new_customer_order_billing_address_allfields',                
                        array(
                            'order_prefix'  => $this->generate('string', 32, ':punct:'),
                            'order_first_name'     => $this->generate('string', 32, ':punct:'),
                            'order_middle_name' => $this->generate('string', 32, ':punct:'),
                            'order_last_name'      => $this->generate('string', 32, ':punct:'),
                            'order_suffix'  => $this->generate('string', 32, ':punct:'),
                            'order_company' =>  $this->generate('string', 32, ':punct:'),
                            'order_street_address_first_line'   => $this->generate('string', 32, ':punct:'),
                            'order_street_address_second_line'  => $this->generate('string', 32, ':punct:'),
                            'order_city'    =>  $this->generate('string', 32, ':punct:'),
                            'order_zip_postal_code' =>  $this->generate('string', 32, ':punct:'),
                            'order_phone'   =>  $this->generate('string', 32, ':punct:'),
                            'order_fax' =>  $this->generate('string', 32, ':punct:'),
                            'email' =>  $this->generate('email', 32, 'valid')
                            )
                );
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
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
        $this->clickControl('radiobutton', 'check_money_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_radio1', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_orders_view'));
        $this->clickButton('invoice', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_order_invoice'));
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_orders_view'));
        $this->clickButton('credit_memo', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_order_creditmemo'));
        $this->clickButton('refund_offline', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_orders_view'));
    }

    /**
     * Create customer via 'Create order' form (all fields are filled with correct data).
     * Create order(all fields are filled).
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
     * 9.Check payment method 'Check / Money order'
     * 
     * 10.Choose first from 'Get shipping methods and rates'.
     * 
     * 11.Submit order.
     * 
     * 12.Invoice order.
     * 
     * 13. Ship order.
     *
     * Expected result:
     *
     * New customer successfully created. Order is created for the new customer, invoiced and credited.
     *
     *
     */    
    public function testOrderCreditAllnumCharacters()
    {
        //Data
        $data = $this->loadData(
                        'new_customer_order_billing_address_allfields',                
                        array(
                            'order_prefix'  => $this->generate('string', 255, ':alnum:'),
                            'order_first_name'     => $this->generate('string', 255, ':alnum:'),
                            'order_middle_name' => $this->generate('string', 255, ':alnum:'),
                            'order_last_name'      => $this->generate('string', 255, ':alnum:'),
                            'order_suffix'  => $this->generate('string', 255, ':alnum:'),
                            'order_company' =>  $this->generate('string', 255, ':alnum:'),
                            'order_street_address_first_line'   => $this->generate('string', 255, ':alnum:'),
                            'order_street_address_second_line'  => $this->generate('string', 255, ':alnum:'),
                            'order_city'    =>  $this->generate('string', 255, ':alnum:'),
                            'order_zip_postal_code' =>  $this->generate('string', 255, ':alnum:'),
                            'order_phone'   =>  $this->generate('string', 255, ':alnum:'),
                            'order_fax' =>  $this->generate('string', 255, ':alnum:'),
                            'email' =>  $this->generate('email', 32, 'valid')
                            )
                );
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
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
        $this->clickControl('radiobutton', 'check_money_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_radio1', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_orders_view'));
        $this->clickButton('invoice', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_order_invoice'));
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_orders_view'));
        $this->clickButton('credit_memo', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_order_creditmemo'));
        $this->clickButton('refund_offline', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('sales_orders_view'));
    }

    
    /**
     * Create customer via 'Create order' form (all fields are filled with correct data, email is invalid).
     * Create order(all fields are filled).
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
     * 9.Check payment method 'Check / Money order'
     * 
     * 10.Choose first from 'Get shipping methods and rates'.
     * 
     * 11.Submit order.
     * 
     * 12.Invoice order.
     * 
     * 13. Ship order.
     *
     * Expected result:
     *
     * New customer successfully created. Order is created for the new customer, invoiced and shipped.
     *
     *
     */    
    public function testOrderCreditInvalidEmailMaxLength()
    {
        //Data
        $data = $this->loadData(
                        'new_customer_order_billing_address_allfields',                
                        array(
                            'order_prefix'  => $this->generate('string', 255, ':alnum:'),
                            'order_first_name'     => $this->generate('string', 255, ':alnum:'),
                            'order_middle_name' => $this->generate('string', 255, ':alnum:'),
                            'order_last_name'      => $this->generate('string', 255, ':alnum:'),
                            'order_suffix'  => $this->generate('string', 255, ':alnum:'),
                            'order_company' =>  $this->generate('string', 255, ':alnum:'),
                            'order_street_address_first_line'   => $this->generate('string', 255, ':alnum:'),
                            'order_street_address_second_line'  => $this->generate('string', 255, ':alnum:'),
                            'order_city'    =>  $this->generate('string', 255, ':alnum:'),
                            'order_zip_postal_code' =>  $this->generate('string', 255, ':alnum:'),
                            'order_phone'   =>  $this->generate('string', 255, ':alnum:'),
                            'order_fax' =>  $this->generate('string', 255, ':alnum:'),
                            'email' =>  $this->generate('email', 255, 'valid')
                            )
                );
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
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
        $this->clickControl('radiobutton', 'check_money_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_radio1', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('new_order_for_new_customer'));
        $this->assertTrue($this->errorMessage('email_exceeds_allowed_length'), $this->messages);
    }

    /**
     * Create customer via 'Create order' form (all fields are filled with correct data, email's hostname is invalid).
     * Create order(all fields are filled).
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
     * 9.Check payment method 'Check / Money order'
     * 
     * 10.Choose first from 'Get shipping methods and rates'.
     * 
     * 11.Submit order.
     * 
     * 12.Invoice order.
     * 
     * 13. Ship order.
     *
     * Expected result:
     *
     * New customer successfully created. Order is created for the new customer, invoiced and shipped.
     *
     *
     */    
    public function testOrderCreditInvalidEmailHostname()
    {
        //Data
        $data = $this->loadData(
                        'new_customer_order_billing_address_allfields',                
                        array(
                            'order_prefix'  => $this->generate('string', 255, ':alnum:'),
                            'order_first_name'     => $this->generate('string', 255, ':alnum:'),
                            'order_middle_name' => $this->generate('string', 255, ':alnum:'),
                            'order_last_name'      => $this->generate('string', 255, ':alnum:'),
                            'order_suffix'  => $this->generate('string', 255, ':alnum:'),
                            'order_company' =>  $this->generate('string', 255, ':alnum:'),
                            'order_street_address_first_line'   => $this->generate('string', 255, ':alnum:'),
                            'order_street_address_second_line'  => $this->generate('string', 255, ':alnum:'),
                            'order_city'    =>  $this->generate('string', 255, ':alnum:'),
                            'order_zip_postal_code' =>  $this->generate('string', 255, ':alnum:'),
                            'order_phone'   =>  $this->generate('string', 255, ':alnum:'),
                            'order_fax' =>  $this->generate('string', 255, ':alnum:'),
                            'email' =>  $this->generate('email', 132, 'valid')
                            )
                );
        //Filling customer's information, address
        $this->orderHelper()->fillNewBillForm($data);
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
        $this->clickControl('radiobutton', 'check_money_order', FALSE);
        $this->pleaseWait();
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'ship_radio1', FALSE);
        $this->pleaseWait();
        $this->clickButton('submit_order', TRUE);
        $this->assertTrue($this->orderHelper()->defineId('new_order_for_new_customer'));
        $this->assertTrue($this->errorMessage('email_is_not_valid_hostname'), $this->messages);
    }
}
