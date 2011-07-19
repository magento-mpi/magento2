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
 * Creating Order for existing customer
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderForExisitingCustomer_Test extends Mage_Selenium_TestCase
{
   /**
    * Preconditions:
    *
    * Log in to Backend.
    */
    public function setUpBeforeTests()
    {
        $this->windowMaximize();
        $this->loginAdminUser();
        $this->OrderHelper()->createProducts('product_to_order1', TRUE);
        $this->OrderHelper()->createProducts('product_to_order2', TRUE);
    }
   /**
    *
    * Create Products for testing.
    * Navigate to Manage Customers page.
    *
    */
    protected function assertPreConditions()
    {}
   /**
    * Create order(all required fields are filled) for existing customer.
    *
    * Steps:
    *
    * 1. Create new customer.
    *
    * 2. Navigate to Sales-Orders.
    *
    * 3. Create order for existing customer.
    *
    * Expected result:
    *
    * Order is created for existing customer.
    *
    * Message "The order has been created." is displayed.
    *
    */
    public function testCreateNewOrderWithRequiredFieldsExistCustomer()
    {
        $userData = $this->loadData('new_customer');
        $addressData = $this->loadData('new_customer_address');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $this->CustomerHelper()->createCustomer($userData, $addressData);
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        $email = array('email'=> $userData['email']);
        $data = array_merge($userData, $addressData);
        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(false, 'products',
            $data, $data,'test_purpose@gmail.com', 'Default Store View', 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, $email);
    }
}
