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
class OrderForNewCustomerCheckRequiredFields_Test extends Mage_Selenium_TestCase
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
        $this->OrderHelper()->createProducts('product_to_order1', TRUE);
        $this->OrderHelper()->createProducts('product_to_order2', TRUE);
    }
   /**
    *
    * Creating products for testing.
    *
    * Navigate to Sales-Orders page.
    *
    */
    protected function assertPreConditions()
    {}
   /**
    * Create customer via 'Create order' form (required fields are not filled).
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
    * 5.Fill all fields except one required.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Check / Money order'
    *
    * 10.Choose first from 'Get shipping methods and rates'.
    *
    * 11.Submit order.
    *
    * Expected result:
    *
    * New customer is not created. Order is not created for the new customer. Message with "Empty required field" appears.
    *
    * @dataProvider data_emptyBillingFields
    *
    * @param array $emptyBillingField
    *
    */
    public function testOrderWithoutRequiredFieldsFilledBillingAddress($emptyBillingField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                $this->loadData(
                        'new_customer_order_billing_address_reqfields',
                        $emptyBillingField), null, null, 'Default Store View',
                true, true,'visa','Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_billing_address');
        foreach ($emptyBillingField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($fieldSet->findField($key) != Null) {
                $fieldXpath = $fieldSet->findField($key);
            } else {
                $fieldXpath = $fieldSet->findDropdown($key);
            }
            if (preg_match('/street_address/', $key)) {
                $fieldXpath .= "/ancestor::div[@class='multi-input']";
            }
            $this->addParameter('fieldXpath', $fieldXpath);
        }   $this->addParameter('fieldXpath', $fieldXpath);
        //Check if message appears.
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }
    public function data_emptyBillingFields()
    {
        return array(
            array(array(    'billing_first_name'     => ''
                            )),
            array(array(
                            'billing_last_name'      => ''
                            )),
            array(array(
                            'billing_street_address_1'   => ''
                            )),
            array(array(
                            'billing_city'    =>  ''
                            )),
            array(array(
                            'billing_zip_code' =>  ''
                            )),
            array(array(
                            'billing_telephone'   =>  ''
                            ))
        );
    }
   /**
    * Create customer via 'Create order' form (required fields are not filled).
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
    * 5.Fill all fields except one required.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Fill in billing address with required fields
    *
    * 8.Check each shipping required fields (message with error should appear near the field)
    *
    * 9.Check payment method 'visa'. Fill its fields with correct information
    *
    * 10.Choose first from 'Get shipping methods and rates'.
    *
    * 11.Submit order.
    *
    * Expected result:
    *
    * New customer is not created. Order is not created for the new customer. Message with "Empty required field" appears.
    *
    * @dataProvider data_emptyShippingFields
    *
    * @param array $emptyShippingField
    *
    */
    public function testOrderWithoutRequiredFieldsFilledShippingAddress($emptyShippingField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                $this->loadData(
                        'new_customer_order_billing_address_reqfields'),
                $this->loadData(
                        'new_customer_order_shipping_address_reqfields',
                        $emptyShippingField), null, 'Default Store View',
                true, true,'visa','Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_shipping_address');
        foreach ($emptyShippingField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($fieldSet->findField($key) != Null) {
                $fieldXpath = $fieldSet->findField($key);
            } else {
                $fieldXpath = $fieldSet->findDropdown($key);
            }
            if (preg_match('/street_address/', $key)) {
                $fieldXpath .= "/ancestor::div[@class='multi-input']";
            }
            $this->addParameter('fieldXpath', $fieldXpath);
        }   $this->addParameter('fieldXpath', $fieldXpath);
        //Check if message appears.
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }
    public function data_emptyShippingFields()
    {
        return array(
            array(array(    'shipping_first_name'     => ''
                            )),
            array(array(
                            'shipping_last_name'      => ''
                            )),
            array(array(
                            'shipping_street_address_1'   => ''
                            )),
            array(array(
                            'shipping_city'    =>  ''
                            )),
            array(array(
                            'shipping_zip_code' =>  ''
                            )),
            array(array(
                            'shipping_telephone'   =>  ''
                            ))
        );
    }
   /**
    * Create order without shipping method
    *
    * Steps:
    *
    * 1. Create new order for new customer
    * 2. Fill in the required fields with billing and shipping address
    * 3. Do not add any products to order
    * 4. Do not choose any shipping method.
    * 5. Submit order
    *
    * Expected result:
    * Order cannot be created by the reason of empty required fields in shipping method
    */
    public function testNoShippingMethodChosen()
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, null,
                $this->loadData(
                        'new_customer_order_billing_address_reqfields'),
                null, null, 'Default Store View', true, true, null, null);
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('shipping_method');
        $fieldXpath = $fieldSet->findLink('get_shipping_methods_and_rates');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }
   /**
    * Create order without products
    *
    * Steps:
    *
    * 1. Create new order for new customer
    * 2. Fill in the required fields with billing and shipping address
    * 3. Add products to order
    * 4. Choose any shipping method.
    * 5. Remove products from order.
    * 6. Submit order
    *
    * Expected result:
    * Order cannot be created. Message 'You need to specify order items.' appears.
    */
    public function testNoProductsChosen()
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                $this->loadData(
                        'new_customer_order_billing_address_reqfields'),
                null, null, 'Default Store View', true, true, 'visa', null);
        $this->clickControl('link', 'get_shipping_methods_and_rates', false);
        $this->waitForAjax();
        $this->addParameter('shipMethod', 'Fixed');
        $this->clickControl('radiobutton', 'ship_method', false);
        $prod1 = $this->loadData('product_to_order1');
        $prod2 = $this->loadData('product_to_order2');
        $productXpath = '//tbody/tr[./td/div[contains(.,'.$prod1['general_sku'].')]]/td[@class="last"]';
        $this->addParameter('productXpath', $productXpath);
        $this->fillForm('products_to_remove');
        $this->clickButton('update_items_and_quantity');
        $productXpath = '//tbody/tr[./td/div[contains(.,'.$prod2['general_sku'].')]]/td[@class="last"]';
        $this->addParameter('productXpath', $productXpath);
        $this->fillForm('products_to_remove');
        $this->clickButton('update_items_and_quantity');
        $this->clickControl('link', 'get_shipping_methods_and_rates', false);
        $this->waitForAjax();
        $this->clickButton('submit_order', FALSE);
        $this->waitForPageToLoad();
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('shipping_method');
        $fieldXpath = $fieldSet->findLink('get_shipping_methods_and_rates');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('error_specify_order_items'), $this->messages);
    }
   /**
    * Create order without payment method
    *
    * Steps:
    *
    * 1. Create new order for new customer
    * 2. Fill in the required fields with billing and shipping address
    * 3. Add products to order
    * 4. Choose any shipping method.
    * 5. Do not choose payment method.
    * 6. Submit order
    *
    * Expected result:
    * Order cannot be created. Message 'Please select one of the options.' appears.
    */
    public function testNoPaymentMethodChosen()
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                $this->loadData(
                        'new_customer_order_billing_address_reqfields'),
                null, null, 'Default Store View', true, true, null, 'Fixed');

        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_payment_method');
        $fieldXpath = $fieldSet->findRadiobutton('credit_card');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('empty_payment_method'), $this->messages);
    }
}
