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
 * Creting Order with promoted product
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_WithPromotedProductsTest extends Mage_Selenium_TestCase
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
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods');
        $payment = $this->loadData('saved_cc_wo3d_enable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
    }

    /**
     * @test
     */
    public function createProducts()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
        $productData = $this->loadData('promoted_product_special_price_for_order',
                null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData;
    }


    /**
     * <p>Order with promoted products. Special Price</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add product with special price.</p>
     * <p>8.Fill in all required fields.</p>
     * <p>9.Check payment method 'PayPal Direct - Visa'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function specialPrices($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_for_promoted_products_special_price');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderData['products_to_reconfigure']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderData = $this->arrayEmptyClear($orderData);
        $this->orderHelper()->navigateToCreateOrderPage();
        $this->orderHelper()->addProductsToOrder($orderData['products_to_add']);
        $this->orderHelper()->reconfigProduct($orderData['products_to_reconfigure']);
        $value = '$'.$productData['prices_special_price'];
        $this->addParameter('value', $value);
        $xpath = $this->_getControlXpath('field', 'price_value');
        $this->assertTrue($this->isElementPresent($xpath));
    }

    /**
     * <p>Order with promoted products. Minimum allowed quantity</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add product with min allowed quantity less then needed.</p>
     * <p>8.Fill in all required fields.</p>
     * <p>9.Check payment method 'PayPal Direct - Visa'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>Warning message appears before submitting order.</p>
     *
     * @depends createProducts
     * @test
     */
    public function minAllowedQtyInShoppingCart($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_for_promoted_products_min_qty');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderData['products_to_reconfigure']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderData = $this->arrayEmptyClear($orderData);
        $this->orderHelper()->navigateToCreateOrderPage();
        $this->orderHelper()->addProductsToOrder($orderData['products_to_add']);
        $this->orderHelper()->reconfigProduct($orderData['products_to_reconfigure']);
        $value = '$'.$productData['prices_special_price'];
        $xpath = $this->_getControlXpath('message', 'invalid_qty_of_product');
        $this->assertTrue($this->isElementPresent($xpath), 'Warning message did not appear');
    }
}
