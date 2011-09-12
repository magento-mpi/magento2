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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_PaymentMethodsTest extends Mage_Selenium_TestCase
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
     * <p>Visa credit card.</p>
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
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function savedCC($productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_data_visa_1');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>Check/Money order.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Check/Money Order'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends createProducts
     * @test
     */
    public function checkMoneyOrder($productData)
    {
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_data_check_money_order_1');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>AuthorizeNet.</p>
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
     * <p>14. Ship order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
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
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods', TRUE);
        $payment = $this->loadData('authorize_net_without_3d_disable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
    }

    /**
     * <p>PayPalUkDirect.</p>
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
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
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
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $paypalukdirect = $this->loadData('paypal_uk_direct_wo_3d_disable');
        $this->fillForm($paypalukdirect, 'sales_paypal');
        $this->saveForm('save_config');
    }
    /**
     * <p>PayflowPro.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Payflow Pro'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
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
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('payflow_pro_wo_3d_disable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
    }

    /**
     * <p>Website payments pro.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPal Direct'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
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
        //Postconditions
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/paypal/');
        $this->clickControl('tab', 'sales_paypal', TRUE);
        $payflowpro = $this->loadData('website_payments_pro_wo_3d_disable');
        $this->fillForm($payflowpro, 'sales_paypal');
        $this->saveForm('save_config');
    }

//TODO (working 50/50. seems 3D sometimes needs to enter password several times)
//    /**
//     * <p>AuthorizeNet.</p>
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
//     * <p>14. Ship order.</p>
//     * <p>Expected result:</p>
//     * <p>New customer is created. Order is created for the new customer.</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function using3DSecureAuthorizeNet($productData)
//    {
//        //Preconditions: Enabling 3DSecure
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment_services/');
//        $this->clickControl('tab', 'sales_payment_services', TRUE);
//        $payflowpro = $this->loadData('3d_secure_enable');
//        $this->fillForm($payflowpro, 'sales_payment_services');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling AuthorizeNet with 3D Secure
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment/');
//        $this->clickControl('tab', 'sales_payment_methods', TRUE);
//        $payment = $this->loadData('authorize_net_with_3d_enable');
//        $this->fillForm($payment, 'sales_payment_methods');
//        $this->saveForm('save_config');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_data_authorize_net_3D_1');
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Postconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment_services/');
//        $this->clickControl('tab', 'sales_payment_methods', TRUE);
//        $payment = $this->loadData('authorize_net_with_3d_disable');
//        $this->fillForm($payment, 'sales_payment_methods');
//        $this->saveForm('save_config');
//    }
//
//    /**
//     * <p>PayPalUkDirect with 3D Secure.</p>
//     * <p>Steps:</p>
//     * <p>1.Go to Sales-Orders.</p>
//     * <p>2.Press "Create New Order" button.</p>
//     * <p>3.Press "Create New Customer" button.</p>
//     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
//     * <p>5.Fill all fields.</p>
//     * <p>6.Press 'Add Products' button.</p>
//     * <p>7.Add any product.</p>
//     * <p>8.Fill in all required fields in address.</p>
//     * <p>9.Check payment method 'PayPalUkDirect'. Enter 3D secure password</p>
//     * <p>10.Fill in all required fields.</p>
//     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
//     * <p>12.Submit order.</p>
//     * <p>Expected result:</p>
//     * <p>New customer is created. Order is created for the new customer.</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function using3DSecurePayPalUKDirect($productData)
//    {
//        //Preconditions: Enabling 3DSecure
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment_services/');
//        $this->clickControl('tab', 'sales_payment_services', TRUE);
//        $payflowpro = $this->loadData('3d_secure_enable');
//        $this->fillForm($payflowpro, 'sales_payment_services');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling PayPal
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $paypal = $this->loadData('paypal_enable');
//        $this->fillForm($paypal, 'sales_paypal');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling PayPalUKDirect
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $paypalukdirect = $this->loadData('paypal_uk_direct_w_3d_enable');
//        $this->fillForm($paypalukdirect, 'sales_paypal');
//        $this->saveForm('save_config');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_data_paypal_direct_payment_payflow_edition_w3d_1');
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Postconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $paypalukdirect = $this->loadData('paypal_uk_direct_wo_3d_disable');
//        $this->fillForm($paypalukdirect, 'sales_paypal');
//        $this->saveForm('save_config');
//    }
//    /**
//     * <p>Website payments pro with 3D Secure.</p>
//     * <p>Steps:</p>
//     * <p>1.Go to Sales-Orders.</p>
//     * <p>2.Press "Create New Order" button.</p>
//     * <p>3.Press "Create New Customer" button.</p>
//     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
//     * <p>5.Fill all fields.</p>
//     * <p>6.Press 'Add Products' button.</p>
//     * <p>7.Add any products.</p>
//     * <p>8.Fill all fields in addresses.</p>
//     * <p>9.Check payment method 'PayPal Direct'. Enter 3D Secure password.</p>
//     * <p>10.Fill in all required fields.</p>
//     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
//     * <p>12.Submit order.</p>
//     * <p>Expected result:</p>
//     * <p>New customer is created. Order is created for the new customer.</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function using3DSecurePayPalDirect($productData)
//    {
//        //Preconditions: Enabling 3DSecure
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment_services/');
//        $this->clickControl('tab', 'sales_payment_services', TRUE);
//        $payflowpro = $this->loadData('3d_secure_enable');
//        $this->fillForm($payflowpro, 'sales_payment_services');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling PayPal
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $payflowpro = $this->loadData('paypal_enable');
//        $this->fillForm($payflowpro, 'sales_paypal');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling Website payments pro
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $payflowpro = $this->loadData('website_payments_pro_w_3d_enable');
//        $this->fillForm($payflowpro, 'sales_paypal');
//        $this->saveForm('save_config');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_data_website_payments_pro_w3d_1');
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Postconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $payflowpro = $this->loadData('website_payments_pro_wo_3d_disable');
//        $this->fillForm($payflowpro, 'sales_paypal');
//        $this->saveForm('save_config');
//    }
//
//    /**
//     * <p>PayflowPro with 3D Secure.</p>
//     * <p>Steps:</p>
//     * <p>1.Go to Sales-Orders.</p>
//     * <p>2.Press "Create New Order" button.</p>
//     * <p>3.Press "Create New Customer" button.</p>
//     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
//     * <p>5.Fill all fields.</p>
//     * <p>6.Press 'Add Products' button.</p>
//     * <p>7.Add any products.</p>
//     * <p>8.Fill all fields in addresses.</p>
//     * <p>9.Check payment method 'Payflow Pro'. Enter 3D secure password.</p>
//     * <p>10.Fill in all required fields.</p>
//     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
//     * <p>12.Submit order.</p>
//     * <p>Expected result:</p>
//     * <p>New customer is created. Order is created for the new customer.</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function using3DSecurePayFlowProVerisign($productData)
//    {
//        //Preconditions: Enabling 3DSecure
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment_services/');
//        $this->clickControl('tab', 'sales_payment_services', TRUE);
//        $payflowpro = $this->loadData('3d_secure_enable');
//        $this->fillForm($payflowpro, 'sales_payment_services');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling PayPal
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $payflowpro = $this->loadData('paypal_enable');
//        $this->fillForm($payflowpro, 'sales_paypal');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling PayflowPro
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $payflowpro = $this->loadData('payflow_pro_w_3d_enable');
//        $this->fillForm($payflowpro, 'sales_paypal');
//        $this->saveForm('save_config');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_data_payflow_pro_w3d_1');
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Postconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/paypal/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $payflowpro = $this->loadData('payflow_pro_wo_3d_disable');
//        $this->fillForm($payflowpro, 'sales_paypal');
//        $this->saveForm('save_config');
//    }
//
//    /**
//     * <p>Visa credit card with 3D Secure.</p>
//     * <p>Steps:</p>
//     * <p>1.Go to Sales-Orders.</p>
//     * <p>2.Press "Create New Order" button.</p>
//     * <p>3.Press "Create New Customer" button.</p>
//     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
//     * <p>5.Fill all fields.</p>
//     * <p>6.Press 'Add Products' button.</p>
//     * <p>7.Add any products.</p>
//     * <p>8.Fill all fields for addresses.</p>
//     * <p>9.Check payment method 'Credit Card - Visa'. Enter 3D secure password.</p>
//     * <p>10. Fill in all required fields.</p>
//     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
//     * <p>12.Submit order.</p>
//     * <p>Expected result:</p>
//     * <p>New customer is created. Order is created for the new customer.</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function using3DSecureSavedCC($productData)
//    {
//        //Preconditions: Enabling 3DSecure
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment_services/');
//        $this->clickControl('tab', 'sales_payment_services', TRUE);
//        $payflowpro = $this->loadData('3d_secure_enable');
//        $this->fillForm($payflowpro, 'sales_payment_services');
//        $this->saveForm('save_config');
//        //Preconditions: Enabling 3D for saved cc.
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment/');
//        $this->clickControl('tab', 'sales_payment_methods', TRUE);
//        $savedcc = $this->loadData('saved_cc_w3d_enable');
//        $this->fillForm($savedcc, 'sales_payment_methods');
//        $this->saveForm('save_config');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_data_visa_w3d_1');
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Postconditions
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/payment/');
//        $this->clickControl('tab', 'sales_paypal', TRUE);
//        $savedcc = $this->loadData('saved_cc_w3d_disable');
//        $this->fillForm($savedcc, 'sales_payment_methods');
//        $this->saveForm('save_config');
//    }
}
