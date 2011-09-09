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
 * Cancel orders
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_CancelTest extends Mage_Selenium_TestCase
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


    /**
     * <p>PayPal Direct. Cancel Order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPal Direct - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Cancel Order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Order is canceled</p>
     *
     * @depends createProducts
     * @test
     */
    public function payPalDirect($productData)
    {
        //Preconditions: Enabling PayPal
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('paypal_enable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
        //Preconditions: Enabling Website payments pro
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('website_payments_pro_wo_3d_enable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_data_website_payments_pro_1');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndChoose(array('1' => $orderId), 'sales_order_grid');
        $userData = array('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('submit');
        $this->assertTrue($this->successMessage('success_canceled_order'), $this->messages);
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('website_payments_pro_wo_3d_disable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
    }

    /**
     * <p>PayPalUK Direct. Cancel Order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPalUkDirect - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Cancel Order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Order is canceled</p>
     *
     * @depends createProducts
     * @test
     */
    public function payPalUKDirect($productData)
    {
        //Preconditions: Enabling PayPal
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $paypal = $this->loadData('paypal_enable');
        $this->fillForm($paypal, 'sales_paypal');
        $this->saveForm('save_config');
        //Preconditions: Enabling PayPalUKDirect
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $paypalukdirect = $this->loadData('paypal_uk_direct_wo_3d_enable');
        $this->fillForm($paypalukdirect, 'sales_paypal');
        $this->saveForm('save_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_data_paypal_direct_payment_payflow_edition_1');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndChoose(array('1' => $orderId), 'sales_order_grid');
        $userData = array('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('submit');
        $this->assertTrue($this->successMessage('success_canceled_order'), $this->messages);
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $paypalukdirect = $this->loadData('paypal_uk_direct_wo_3d_disable');
        $this->fillForm($paypalukdirect, 'sales_paypal');
        $this->saveForm('save_config');
    }

    /**
     * <p>Verisign. Cancel Order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Verisign - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Cancel Order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Capture Online is successful</p>
     *
     * @depends createProducts
     * @test
     */
    public function payFlowProVerisign($productData)
    {
        //Preconditions: Enabling PayPal
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('paypal_enable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
        //Preconditions: Enabling PayflowPro
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('payflow_pro_wo_3d_enable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_data_payflow_pro_1');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndChoose(array('1' => $orderId), 'sales_order_grid');
        $userData = array('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('submit');
        $this->assertTrue($this->successMessage('success_canceled_order'), $this->messages);
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('payflow_pro_wo_3d_disable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
    }

    /**
     * <p>AuthorizeNet. Cancel Order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'AuthorizeNet - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Cancel Order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Order is Canceled</p>
     *
     * @depends createProducts
     * @test
     */
    public function authorizeNet($productData)
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
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndChoose(array('1' => $orderId), 'sales_order_grid');
        $userData = array('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('submit');
        $this->assertTrue($this->successMessage('success_canceled_order'), $this->messages);
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/payment_services/');
        $this->clickControl('tab', 'sales_payment_methods', TRUE);
        $payment = $this->loadData('authorize_net_without_3d_disable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
    }
}
