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
 * AuthorizeNet CreditMemo
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderCreditMemo_CreateWithAuthorizeNetTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'System Configuration' page</p>
     * <p>Enable all shipping methods</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
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

//    /**
//     * <p>AuthorizeNet. Refund Online</p>
//     * <p>Steps:</p>
//     * <p>1.Go to Sales-Orders.</p>
//     * <p>2.Press "Create New Order" button.</p>
//     * <p>3.Press "Create New Customer" button.</p>
//     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
//     * <p>5.Fill all fields.</p>
//     * <p>6.Press 'Add Products' button.</p>
//     * <p>7.Add first two products.</p>
//     * <p>8.Choose shipping address the same as billing.</p>
//     * <p>9.Check payment method 'Credit Card - Visa'</p>
//     * <p>10. Fill in all required fields.</p>
//     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
//     * <p>12.Submit order.</p>
//     * <p>13.Invoice order.</p>
//     * <p>14.Make refund online.</p>
//     * <p>Expected result:</p>
//     * <p>New customer is created. Order is created for the new customer. Refund Online is successful</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function fullRefundOnline($productData)
//    {
//        //Preconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment/');
//        $this->clickControl('tab', 'sales_payment_methods', TRUE);
//        $payment = $this->loadData('authorize_net_without_3d_enable');
//        $this->fillForm($payment, 'sales_payment_methods');
//        $this->saveForm('save_config');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_data_authorize_net_1');
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        $this->addParameter('order_id', $orderId);
//        $this->addParameter('id', $this->defineIdFromUrl());
//        $this->clickButton('invoice', TRUE);
//        $this->fillForm(array('amount' => 'Capture Online'));
//        $this->clickButton('submit_invoice', TRUE);
//        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
//        $this->navigate('manage_sales_invoices');
//        $this->searchAndOpen(array('filter_order_id' => $orderId), FALSE);
//        $this->waitForPageToLoad();
//        $this->addParameter('invoice_id', $this->defineIdFromUrl());
//        $this->clickButton('credit_memo', TRUE);
//        $this->clickButton('refund', TRUE);
//        $this->assertTrue($this->successMessage('success_creating_creditmemo'), $this->messages);
//        //Postconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment/');
//        $this->clickControl('tab', 'sales_payment_methods', TRUE);
//        $payment = $this->loadData('authorize_net_without_3d_disable');
//        $this->fillForm($payment, 'sales_payment_methods');
//        $this->saveForm('save_config');
//    }

    /**
     * <p>AuthorizeNet. Refund Offline</p>
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
     * <p>14.Make refund offline.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Refund Offline is successful</p>
     *
     * @depends createProducts
     * @test
     */
    public function fullRefundOffline($productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods', TRUE);
        $payment = $this->loadData('authorize_net_without_3d_enable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_data_authorize_net_1');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice', TRUE);
        $this->fillForm(array('amount' => 'Capture Offline'));
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->deleteElement('credit_memo', 'confirmation_to_procced');
        $this->clickButton('refund_offline', TRUE);
        $this->assertTrue($this->successMessage('success_creating_creditmemo'), $this->messages);
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods', TRUE);
        $payment = $this->loadData('authorize_net_without_3d_disable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
    }

}
