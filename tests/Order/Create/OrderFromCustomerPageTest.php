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
 * Creating Order for existing customer from customers' page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderFromCustomerPage_Test extends Mage_Selenium_TestCase
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
     * Navigate to Manage Customers page.
     * 
     */    
    protected function assertPreConditions()
    {
        $this->orderHelper()->createProducts('product_to_order1');
        $this->orderHelper()->createProducts('product_to_order2');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }
    
    
    
    /**
     * Create order(all required fields are filled) for existing customer from customer's page.
     *
     * Steps:
     *
     * 1.Create new customer.
     * 
     * 2.Open customer's information page.
     * 
     * 3.Create order for existing customer.
     *
     * Expected result:
     *
     * Order is created for existing customer.
     *
     * Message "The order has been created." is displayed.
     *
     */
    
    
    //Creating customer.
    public function testCreateCustomer()
    {
        $userData = $this->loadData('new_customer');
        $addressData = $this->loadData('new_customer_address');
        $this->CustomerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');

        return $userData;
        
    }
    
    public function testCreateNewOrderWithRequiredFieldsExistCustomer()
    {
        $userData = $this->loadData('new_customer');
        $this->orderHelper()->defineId('manage_customers');
        $searchData = array('email' => $userData['email'], 'last_name' => $userData['last_name']);
        $this->searchAndOpen($searchData, TRUE);
        $this->orderHelper()->defineId('edit_customer');
        $this->assertTrue($this->clickButton('create_order', TRUE), 'Navigated to Create New Order page');
        $this->orderHelper()->defineId('new_order_for_existing_customer');

        $customerAddress = array ('order_billing_address_choice' => 'Steven Stevenson, number 11 nothing, nowhere, 90232, Ukraine');
        $this->fillForm($customerAddress, 'order_billing_address');
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
        
    }
    
    //Covering up traces. Canceling order
    public function testCancelPendingOrders()
    {
        $data = $this->loadData('new_customer');
        $searchParam = $data['last_name'];
        $this->orderHelper()->cancelPendingOrders($searchParam);
    }
    
    //Covering up traces. Deleting Customer
    public function testDeleteCustomer()
    {
        $searchData = array('name'=>"Stevenson", 'email'=> "test_purpose@gmail.com");
        $this->CustomerHelper()->openCustomer($searchData);
        $this->deleteElement('delete_customer', 'confirmation_for_delete');
        
    }
    
 
}
