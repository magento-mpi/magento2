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
        $this->addParameter('id', '0');
    }

    /**
     * Create Simple Product for tests
     *
     * @test
     */
    public function createSimpleProduct()
    {
        //Data
        $simpleSku = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($simpleSku);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simpleSku['general_sku'];
    }

    /**
     * <p>AuthorizeNet. Full refund/p>
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
     * <p>14.Make refund online.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Credit memo is(isn't) created</p>
     *
     * @depends createSimpleProduct
     * @dataProvider dataRefund
     * @test
     */
    public function fullRefund($captureType, $refundType, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_authorizenet_flatrate', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('authorizenet_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        $orderId = $this->orderHelper()->defineOrderIdFromTitle();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->clickButton('credit_memo');
        $this->clickButton($refundType);
        //Verifying
        if ($refundType != 'refund') {
            $this->assertTrue($this->successMessage('success_creating_creditmemo'), $this->messages);
        } else {
            $this->assertTrue($this->errorMessage('failed_authorize_online_refund'), $this->messages);
        }
    }

    public function dataRefund()
    {
        return array(
            array('Capture Online', 'refund'),
            array('Capture Online', 'refund_offline'),
            array('Capture Offline', 'refund_offline')
        );
    }

    /**
     * @depends createSimpleProduct
     * @dataProvider dataRefund
     * @test
     */
    public function partialRefund($captureType, $refundType, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_authorizenet_flatrate',
                array('filter_sku' => $simpleSku, 'product_qty' => 10));
        $creditMemo = $this->loadData('products_to_refund', array('return_filter_sku' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('authorizenet_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        $orderId = $this->orderHelper()->defineOrderIdFromTitle();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        //Verifying
        if ($refundType != 'refund') {
            $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType);
        } else {
            $this->clickButton('credit_memo');
            $this->clickButton($refundType);
            $this->assertTrue($this->errorMessage('failed_authorize_online_refund'), $this->messages);
        }
    }

}
