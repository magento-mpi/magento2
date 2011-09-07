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
class CheckingValidationBackendTest extends Mage_Selenium_TestCase
{
   /**
    * <p>Preconditions:</p>
    *
    * <p>Log in to Backend.</p>
    *
    */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }
    /**
     *
     * <p>Creating products for testing.</p>
     *
     * <p>Navigate to Sales-Orders page.</p>
     *
     */
    protected function assertPreConditions()
    {}

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

   /**
    * <p>Create customer via 'Create order' form (required fields are not filled).</p>
    * <p>Steps:</p>
    * <p>1.Go to Sales-Orders;</p>
    * <p>2.Press "Create New Order" button;</p>
    * <p>3.Press "Create New Customer" button;</p>
    * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
    * <p>5.Fill all fields except one required;</p>
    * <p>6.Press 'Add Products' button;</p>
    * <p>7.Add first two products;</p>
    * <p>8.Choose shipping address the same as billing;</p>
    * <p>9.Check payment method 'Check / Money order';</p>
    * <p>10.Choose first from 'Get shipping methods and rates';</p>
    * <p>11.Submit order;</p>
    * <p>Expected result:</p>
    * <p>New customer is not created. Order is not created for the new customer. Message with "Empty required field" appears.</p>
    *
    * @depends createProducts
    * @dataProvider data_emptyBillingFields
    * @param array $emptyBillingField
    * @test
    *
    */
    public function orderWithoutRequiredFieldsFilledBillingAddress($emptyBillingField, $productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_req_1', $emptyBillingField);
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderHelper()->createOrder($orderData, true);
        $this->clickButton('submit_order', FALSE);
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
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }
    public function data_emptyBillingFields()
    {
        return array(
            array(array('billing_first_name'     => '')),
            array(array('billing_last_name'      => '')),
            array(array('billing_street_address_1'   => '')),
            array(array('billing_city'    =>  '')),
            array(array('billing_zip_code' =>  '')),
            array(array('billing_telephone'   =>  ''))
        );
    }
   /**
    * <p>Create customer via 'Create order' form (required fields are not filled).</p>
    * <p>Steps:</p>
    * <p>1.Go to Sales-Orders;</p>
    * <p>2.Press "Create New Order" button;</p>
    * <p>3.Press "Create New Customer" button;</p>
    * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
    * <p>5.Fill all fields except one required;</p>
    * <p>6.Press 'Add Products' button;</p>
    * <p>7.Fill in billing address with required fields;</p>
    * <p>8.Check each shipping required fields (message with error should appear near the field);</p>
    * <p>9.Check payment method 'visa'. Fill its fields with correct information;</p>
    * <p>10.Choose first from 'Get shipping methods and rates';</p>
    * <p>11.Submit order;</p>
    * <p>Expected result:</p>
    * <p>New customer is not created. Order is not created for the new customer. Message with "Empty required field" appears.</p>
    *
    * @depends createProducts
    * @dataProvider data_emptyShippingFields
    * @param array $emptyShippingField
    * @test
    */
    public function orderWithoutRequiredFieldsFilledShippingAddress($emptyShippingField, $productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_req_1', $emptyShippingField);
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderHelper()->createOrder($orderData, true);
        $this->clickButton('submit_order', FALSE);
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
    * <p>Create order without shipping method</p>
    * <p>Steps:</p>
    * <p>1. Create new order for new customer;</p>
    * <p>2. Fill in the required fields with billing and shipping address;</p>
    * <p>3. Do not add any products to order;</p>
    * <p>4. Do not choose any shipping method;</p>
    * <p>5. Submit order;</p>
    * <p>Expected result:</p>
    * <p>Order cannot be created by the reason of empty required fields in shipping method.</p>
    *
    * @depends createProducts
    * @test
    */
    public function noShippingMethodChosen($productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_no_shipping_method');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderHelper()->createOrder($orderData, true);
        $this->clickButton('submit_order', FALSE);
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('shipping_method');
        $fieldXpath = $fieldSet->findLink('get_shipping_methods_and_rates');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }
   /**
    * <p>Create order without products.</p>
    * <p>Steps:</p>
    * <p>1. Create new order for new customer;</p>
    * <p>2. Fill in the required fields with billing and shipping address;</p>
    * <p>3. Add products to order;</p>
    * <p>4. Choose any shipping method;</p>
    * <p>5. Remove products from order;</p>
    * <p>6. Submit order;</p>
    * <p>Expected result:</p>
    * <p>Order cannot be created. Message 'You need to specify order items.' appears.</p>
    *
    * @depends createProducts
    * @test
    */
    public function noProductsChosen($productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_no_shipping_method');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderHelper()->createOrder($orderData, true);
        $productToRemove = $this->loadData('products_to_reconfig_2');
        $productToRemove['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderHelper()->reconfigProduct($productToRemove);
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
    * <p>Create order without payment method.</p>
    * <p>Steps:</p>
    * <p>1. Create new order for new customer;</p>
    * <p>2. Fill in the required fields with billing and shipping address;</p>
    * <p>3. Add products to order;</p>
    * <p>4. Choose any shipping method;</p>
    * <p>5. Do not choose payment method;</p>
    * <p>6. Submit order;</p>
    * <p>Expected result:</p>
    * <p>Order cannot be created. Message 'Please select one of the options.' appears.</p>
    *
    * @depends createProducts
    * @test
    */
    public function noPaymentMethodChosen($productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_no_payment_method');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderHelper()->createOrder($orderData, true);
        $this->clickButton('submit_order', FALSE);
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_payment_method');
        $fieldXpath = $fieldSet->findRadiobutton('credit_card');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('empty_payment_method'), $this->messages);
    }
}
