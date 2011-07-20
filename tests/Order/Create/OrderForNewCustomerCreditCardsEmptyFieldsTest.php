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
class OrderForNewCustomerCreditCardsEmptyFields_Test extends Mage_Selenium_TestCase
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
        $this->orderHelper()->createProducts('product_to_order1');
        $this->orderHelper()->createProducts('product_to_order2');
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
        $this->addParameter('id', '0');
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
    * 10. Fill in all required fields, except one.
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
    * New customer is not created. Order is not created for the new customer. Messages for credit card fieldset appear.
    *
    * @dataProvider data_emptyVisaFields
    *
    * @param array $emptyVisaField
    *
    */
    public function testOrderWithEmptyFieldsForCreditCardVisa($emptyVisaField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                'new_customer_order_billing_address_reqfields', null,
                null, 'Default Store View', true, true,
                $this->loadData('visa', $emptyVisaField),'Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_payment_method');
        foreach ($emptyVisaField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($key == 'name_on_card_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            if ($key == 'credit_card_type_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'credit_card_number_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown('credit_card_type_saved');
                }
            }
            if ($key == 'expiration_date_month_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'expiration_date_year_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'cvv_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            $this->addParameter('fieldXpath', $fieldXpath);
            switch ($key) {
            case 'name_on_card_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_type_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_number_saved':
                $this->assertTrue($this->errorMessage('card_type_doesnt_match'), $this->messages);
                break;
            case 'expiration_date_month_saved':
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->assertTrue($this->errorMessage('error_invalid_exp_date'), $this->messages);
                break;
            case 'expiration_date_year_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'cvv_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            default:
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            }
        }
    }
    public function data_emptyVisaFields()
    {
        return array(
            array(array('name_on_card_saved'     => '')),
            array(array('credit_card_type_saved'      => '')),
            array(array('credit_card_number_saved'   => '')),
            array(array('expiration_date_month_saved'    =>  'Month')),
            array(array('expiration_date_year_saved' =>  'Year')),
            array(array('cvv_saved'   =>  ''))
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
    * 5.Fill all fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Credit Card - AmericanExpress'
    *
    * 10. Fill in all required fields, except one.
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
    * New customer is not created. Order is not created for the new customer. Messages for credit card fieldset appear.
    *
    * @dataProvider data_emptyAmericanExpressFields
    *
    * @param array $emptyAmericanExpressField
    *
    */
    public function testOrderWithEmptyFieldsForCreditCardAmericanExpress($emptyAmericanExpressField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                'new_customer_order_billing_address_reqfields', null,
                null, 'Default Store View', true, true,
                $this->loadData('visa', $emptyAmericanExpressField),'Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_payment_method');
        foreach ($emptyAmericanExpressField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($key == 'name_on_card_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            if ($key == 'credit_card_type_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'credit_card_number_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown('credit_card_type_saved');
                }
            }
            if ($key == 'expiration_date_month_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'expiration_date_year_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'cvv_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            $this->addParameter('fieldXpath', $fieldXpath);
            switch ($key) {
            case 'name_on_card_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_type_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_number_saved':
                $this->assertTrue($this->errorMessage('card_type_doesnt_match'), $this->messages);
                break;
            case 'expiration_date_month_saved':
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->assertTrue($this->errorMessage('error_invalid_exp_date'), $this->messages);
                break;
            case 'expiration_date_year_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'cvv_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            default:
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            }
        }
    }
    public function data_emptyAmericanExpressFields()
    {
        return array(
            array(array('name_on_card_saved'     => '')),
            array(array('credit_card_type_saved'      => '')),
            array(array('credit_card_number_saved'   => '')),
            array(array('expiration_date_month_saved'    =>  'Month')),
            array(array('expiration_date_year_saved' =>  'Year')),
            array(array('cvv_saved'   =>  ''))
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
    * 10. Fill in all required fields, except one.
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
    * New customer is not created. Order is not created for the new customer. Messages for credit card fieldset appear.
    *
    * @dataProvider data_emptyMasterCardFields
    *
    * @param array $emptyMasterCardField
    *
    */
    public function testOrderWithEmptyFieldsForCreditCardMasterCard($emptyMasterCardField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                'new_customer_order_billing_address_reqfields', null,
                null, 'Default Store View', true, true,
                $this->loadData('visa', $emptyMasterCardField),'Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_payment_method');
        foreach ($emptyMasterCardField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($key == 'name_on_card_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            if ($key == 'credit_card_type_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'credit_card_number_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown('credit_card_type_saved');
                }
            }
            if ($key == 'expiration_date_month_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'expiration_date_year_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'cvv_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            $this->addParameter('fieldXpath', $fieldXpath);
            switch ($key) {
            case 'name_on_card_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_type_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_number_saved':
                $this->assertTrue($this->errorMessage('card_type_doesnt_match'), $this->messages);
                break;
            case 'expiration_date_month_saved':
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->assertTrue($this->errorMessage('error_invalid_exp_date'), $this->messages);
                break;
            case 'expiration_date_year_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'cvv_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            default:
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            }
        }
    }
    public function data_emptyMasterCardFields()
    {
        return array(
            array(array('name_on_card_saved'     => '')),
            array(array('credit_card_type_saved'      => '')),
            array(array('credit_card_number_saved'   => '')),
            array(array('expiration_date_month_saved'    =>  'Month')),
            array(array('expiration_date_year_saved' =>  'Year')),
            array(array('cvv_saved'   =>  ''))
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
    * 5.Fill all fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Credit Card - Discover'
    *
    * 10. Fill in all required fields, except one.
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
    * New customer is not created. Order is not created for the new customer. Messages for credit card fieldset appear.
    *
    * @dataProvider data_emptyDiscoverFields
    *
    * @param array $emptyDiscoverField
    *
    */
    public function testOrderWithEmptyFieldsForCreditCardDiscover($emptyDiscoverField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                'new_customer_order_billing_address_reqfields', null,
                null, 'Default Store View', true, true,
                $this->loadData('visa', $emptyDiscoverField),'Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_payment_method');
        foreach ($emptyDiscoverField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($key == 'name_on_card_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            if ($key == 'credit_card_type_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'credit_card_number_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown('credit_card_type_saved');
                }
            }
            if ($key == 'expiration_date_month_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'expiration_date_year_saved'){
                if ($fieldSet->findDropdown($key) != Null)
                {
                    $fieldXpath = $fieldSet->findDropdown($key);
                }
            }
            if ($key == 'cvv_saved'){
                if ($fieldSet->findField($key) != Null)
                {
                    $fieldXpath = $fieldSet->findField($key);
                }
            }
            $this->addParameter('fieldXpath', $fieldXpath);
            switch ($key) {
            case 'name_on_card_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_type_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'credit_card_number_saved':
                $this->assertTrue($this->errorMessage('card_type_doesnt_match'), $this->messages);
                break;
            case 'expiration_date_month_saved':
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->assertTrue($this->errorMessage('error_invalid_exp_date'), $this->messages);
                break;
            case 'expiration_date_year_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            case 'cvv_saved':
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            default:
                $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
                break;
            }
        }
    }
    public function data_emptyDiscoverFields()
    {
        return array(
            array(array('name_on_card_saved'     => '')),
            array(array('credit_card_type_saved'      => '')),
            array(array('credit_card_number_saved'   => '')),
            array(array('expiration_date_month_saved'    =>  'Month')),
            array(array('expiration_date_year_saved' =>  'Year')),
            array(array('cvv_saved'   =>  ''))
        );
    }
}
