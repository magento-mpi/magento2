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
 * Tests for payment methods
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_PaymentMethodsTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Log in to Backend.</p>
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
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $productData['general_sku'];
    }

    /**
     * <p>Create Orders using differnt payment methods without 3DSecure</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Press 'Add Products' button.</p>
     * <p>6.Add simple product.</p>
     * <p>7.Fill all required fields in billing address form.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check shipping method</p>
     * <p>10.Check payment method</p>
     * <p>11.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createSimpleProduct
     * @dataProvider dataWithout3DSecure
     * @test
     */
    public function createOrderWithout3DSecure($payment, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_' . $payment . '_flatrate', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        if ($payment == 'paypaldirect' || $payment == 'paypaldirectuk') {
            $this->systemConfigurationHelper()->configure('paypal_enable');
        }
        if ($payment != 'checkmoney') {
            $payment .= '_without_3Dsecure';
        }
        $this->systemConfigurationHelper()->configure($payment);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataWithout3DSecure()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('authorizenet')
        );
    }

    /**
     * <p>Create Orders using differnt payment methods with 3DSecure</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Press 'Add Products' button.</p>
     * <p>6.Add simple product.</p>
     * <p>7.Fill all required fields in billing address form.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check shipping method</p>
     * <p>10.Check payment method</p>
     * <p>11.Validate card with 3D secure</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createSimpleProduct
     * @dataProvider dataWith3DSecure
     * @test
     */
    public function createOrderWith3DSecure($payment, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_' . $payment . '_flatrate', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        if ($payment == 'paypaldirect' || $payment == 'paypaldirectuk') {
            $this->systemConfigurationHelper()->configure('paypal_enable');
        }
        $this->systemConfigurationHelper()->configure($payment . '_with_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataWith3DSecure()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('authorizenet')
        );
    }

}
