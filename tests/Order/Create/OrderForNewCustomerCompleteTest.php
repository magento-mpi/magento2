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
 * Creating order for new customer
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderForNewCustomerComplete_Test extends Mage_Selenium_TestCase
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
     * <p>Create customer via 'Create order' form (all fields are filled with special chars).</p>
     * <p>Create order(all fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all fields with special characters;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11.Submit order;</p>
     * <p>12.Invoice order;</p>
     * <p>13. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer, invoiced and shipped.</p>
     *
     * @depends createProducts
     * @test
     */
    public function orderCompleteSpecialCharacters($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'shipping', $symNum = 32, TRUE),
                'visa','Fixed');
        $this->clickButton('invoice', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('submit_invoice', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('ship', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('submit_shipment', TRUE);
        $this->defineIdFromUrl();
    }
    /**
     * <p>Create customer via 'Create order' form (all fields are filled).</p>
     * <p>Create order(all fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     *
     * @depends createProducts
     * @test
     */
    public function orderCompleteAllFields($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'billing', $symNum = 32, FALSE),
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'shipping', $symNum = 32, FALSE),
                'visa','Fixed');
        $this->clickButton('invoice', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('submit_invoice', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('ship', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('submit_shipment', TRUE);
        $this->defineIdFromUrl();
    }
    /**
     * <p>Create customer via 'Create order' form (required fields are filled).</p>
     * <p>Create order(required fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons);</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products (select third options for second product);</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     *
     * @depends createProducts
     * @test
     */
    public function orderCompleteReqFields($productData)
    {
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', $products, $email,
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'shipping', $symNum = 32, TRUE),
                'visa','Fixed');
        $this->clickButton('invoice', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('submit_invoice', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('ship', TRUE);
        $this->defineIdFromUrl();
        $this->clickButton('submit_shipment', TRUE);
        $this->defineIdFromUrl();
    }
}
