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
 * Ship fully and partially the products in order
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderShipment_CreateTest extends Mage_Selenium_TestCase
{
   /**
    * <p>Preconditions:</p>
    * <p>Log in to Backend.</p>
    */
   public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->addParameter('tabName', '');
        $this->addParameter('webSite', '');
        $this->addParameter('storeName', '');
        $this->systemConfigurationHelper()->configure('saved_cc_wo3d_enable');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
    }
    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }
    /**
     * @test
     */
    public function createProducts()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData;
    }

    /**
     * <p>TL-MAGE-316:Shipment for order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Credit Card';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Invoice order;</p>
     * <p>13. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced and shipped successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function full($productData)
    {
        $orderData = $this->loadData('order_req_1', array('filter_sku' => $productData['general_sku']));
        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice');
        $this->clickButton('submit_invoice');
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $productsToShip = $this->loadData('products_to_ship_1');
        $productsToShip['product_1']['filter_sku'] = $productData['general_sku'];
        $productsToShip['product_1']['qty_to_ship'] = '1';
        $this->orderShipmentHelper()->createShipment($productsToShip);
    }

    /**
     * <p>TL-MAGE-317:Shipment for part of order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add several products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Credit Card';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Invoice order;</p>
     * <p>13. Partially ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced and shipped successfully</p>
     *
     * @depends createProducts
     * @test
     */
    public function partial($productData)
    {
        $orderData = $this->loadData('order_req_partial_shipment', array('filter_sku' => $productData['general_sku']));
        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice');
        $this->clickButton('submit_invoice');
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $productsToShip = $this->loadData('products_to_ship_1');
        $productsToShip['product_1']['filter_sku'] = $productData['general_sku'];
        $this->orderShipmentHelper()->createShipment($productsToShip);
    }
}
