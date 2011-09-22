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
 * Tests for PayPalUKDirect invoices
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderInvoice_CreateWithPayPalUKDirectTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('paypal_enable');
        $this->systemConfigurationHelper()->configure('paypal_uk_direct_without_3Dsecure');
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
     * <p>PaypalUKDirect. Full Invoice With different types of Capture</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPalUkDirect'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Create Invoice.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Invoice is created</p>
     *
     * @depends createSimpleProduct
     * @dataProvider dataCaptureType
     * @test
     */
    public function fullInvoiceWithDifferentTypesOfCapture($captureType, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_paypaldirectuk_flatrate', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        //Steps
        $orderId = $this->orderHelper()->defineOrderIdFromTitle();
        $this->addParameter('order_id', $orderId);
        $this->clickButton('invoice');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('create_invoice'), $this->messages);
        //Steps
        $this->fillForm(array('amount' => $captureType));
        $this->clickButton('submit_invoice');
        //Verifying
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
    }

    public function dataCaptureType()
    {
        return array(
            array('Capture Online'),
            array('Capture Offline'),
            array('Not Capture')
        );
    }

    /**
     *
     * @param type $captureType
     * @param type $simpleSku
     *
     * @depends createSimpleProduct
     * @dataProvider dataCaptureType
     */
    public function partialInvoiceWithDifferentTypesOfCapture($captureType, $simpleSku)
    {
        $this->markTestSkipped('Need Implement');
    }

}
