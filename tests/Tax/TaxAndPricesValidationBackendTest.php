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
 * Prices Validation on the Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_TaxAndPricesValidationBackendTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
        $this->loginAdminUser();
    }

    /**
     * Create Customer for tests
     *
     * @test
     */
    public function createCustomer()
    {
        //Preconditions
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        //Steps
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);

        return $userData['email'];
    }

    /**
     * Create Category for tests
     *
     * @test
     */
    public function createCategory()
    {
        //Data
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_for_prices_validation', NULL, 'name');
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->categoryHelper()->checkCategoriesPage();

        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * Create Order on the backend and validate prices with taxes
     *
     * @dataProvider dataSystemConfiguration
     * @depends createCategory
     * @depends createCustomer
     *
     * @test
     */
    public function createOrderBackend($dataProv, $category, $customer)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($dataProv);
        //Data for order creation
        $createOrder = $this->loadData($dataProv .
                '_backend_create_order', array('email' => $customer));
        //Data for prices and total verification after order creation
        $verifyPricesAfterOrderCreation = $this->loadData($dataProv .
                '_backend_product_prices_after_order_creation');
        $verifyTotalAfterOrderCreation = $this->loadData($dataProv .
                '_backend_total_after_order_creation');
        //Data for prices and total verification before invoice creation
        $verifyPricesBeforeInvoiceCreation = $this->loadData($dataProv .
                '_backend_product_prices_before_invoice_creation');
        $verifyTotalBeforeInvoiceCreation = $this->loadData($dataProv .
                '_backend_total_before_invoice_creation');
        //Data for prices and total verification after invoice creation on order page
        $verifyPricesAfterInvoiceCreation = $this->loadData($dataProv .
                '_backend_product_prices_after_invoice_creation');
        $verifyTotalAfterInvoiceCreation = $this->loadData($dataProv .
                '_backend_total_after_invoice_creation');
        //Data for prices and total verification after invoice creation on invoice page
        $verifyPricesAfterInvoiceCreationInvoicePage = $this->loadData($dataProv .
                '_backend_product_prices_after_invoice_creation_invoice_page');
        $verifyTotalAfterInvoiceCreationInvoicePage = $this->loadData($dataProv .
                '_backend_total_after_invoice_creation_invoice_page');
        //Data for prices and total verification after invoice creation on invoice page
        $verifyPricesAfterShipmentCreation = $this->loadData($dataProv .
                '_backend_product_prices_after_shipment_creation');
        $verifyTotalAfterShipmentCreation = $this->loadData($dataProv .
                '_backend_total_after_shipment_creation');
        //Data for prices and total verification before refund creation on refund page
        $verifyPricesBeforeRefundCreation = $this->loadData($dataProv .
                '_backend_product_prices_before_refund_creation');
        $verifyTotalBeforeRefundCreation = $this->loadData($dataProv .
                '_backend_total_before_refund_creation');
        //Data for prices and total verification after refund creation on order page
        $verifyPricesAfterRefundCreation = $this->loadData($dataProv .
                '_backend_product_prices_after_refund_creation');
        $verifyTotalAfterRefundCreation = $this->loadData($dataProv .
                '_backend_total_after_refund_creation');
        //Data for prices and total verification after refund creation on refund page
        $verifyPricesAfterRefundCreationRefundPage = $this->loadData($dataProv .
                '_backend_product_prices_after_refund_creation_refund_page');
        $verifyTotalAfterRefundCreationRefundPage = $this->loadData($dataProv .
                '_backend_total_after_refund_creation_refund_page');

        for ($i=1; $i <= 3; $i++) {
            $simpleProductData = $this->loadData("simple_product_for_prices_validation_$i",
                array('categories' => $category), array('general_name', 'general_sku'));
            $this->navigate('manage_products');
            $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
            $this->productHelper()->createProduct($simpleProductData);
            $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
            $createOrder['products_to_add']['product_' . $i]['filter_sku'] = $simpleProductData['general_sku'];
            $createOrder['prod_verification']['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesAfterOrderCreation['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesBeforeInvoiceCreation['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesAfterInvoiceCreation['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesAfterInvoiceCreationInvoicePage['product_' .
                    $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesAfterShipmentCreation['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesBeforeRefundCreation['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesAfterRefundCreation['product_' . $i]['product'] = $simpleProductData['general_name'];
            $verifyPricesAfterRefundCreationRefundPage['product_' . $i]['product'] = $simpleProductData['general_name'];
        }
        //Create Order and validate prices during order creation
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $this->orderHelper()->createOrder($createOrder);
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        //Define Order Id to work with
        $orderId = $this->orderHelper()->defineOrderIdFromTitle();
        //Verify prices on order review page after order creation
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesAfterOrderCreation,
                $verifyTotalAfterOrderCreation);
        //Verify prices before creating Invoice
        $this->clickButton('invoice');
        $this->assertTrue($this->checkCurrentPage('create_invoice'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesBeforeInvoiceCreation,
                $verifyTotalBeforeInvoiceCreation);
        //Verify prices after creating Invoice on order page
        $this->clickButton('submit_invoice');
        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesAfterInvoiceCreation,
                $verifyTotalAfterInvoiceCreation);
        //Verify prices after creating Invoice on invoice page
        $this->navigate('manage_sales_invoices');
        $this->assertTrue($this->checkCurrentPage('manage_sales_invoices'), $this->messages);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->assertTrue($this->checkCurrentPage('view_invoice'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesAfterInvoiceCreationInvoicePage,
                $verifyTotalAfterInvoiceCreationInvoicePage);
        //Verify prices after creating Shipment on order page
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $this->searchAndOpen(array('filter_id' => $orderId));
        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
        $this->clickButton('ship');
        $this->assertTrue($this->checkCurrentPage('create_shipment'), $this->messages);
        $this->clickButton('submit_shipment');
        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesAfterShipmentCreation,
                $verifyTotalAfterShipmentCreation);
        //Verify prices before creating Refund on refund page
        $this->clickButton('credit_memo');
        $this->assertTrue($this->checkCurrentPage('create_credit_memo'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesBeforeRefundCreation,
                $verifyTotalBeforeRefundCreation);
        //Verify prices after creating Refund on order page
        $this->clickButton('refund_offline');
        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesAfterRefundCreation,
                $verifyTotalAfterRefundCreation);
        //Verify prices after creating Refund on Refund page
        $this->navigate('manage_sales_creditmemos');
        $this->assertTrue($this->checkCurrentPage('manage_sales_creditmemos'), $this->messages);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->assertTrue($this->checkCurrentPage('view_credit_memo'), $this->messages);
        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyPricesAfterRefundCreationRefundPage,
                $verifyTotalAfterRefundCreationRefundPage);

    }

    public function dataSystemConfiguration()
    {
        return array(
            array('unit_cat_ex_ship_ex'),
            array('row_cat_ex_ship_ex'),
            array('total_cat_ex_ship_ex'),
            array('unit_cat_ex_ship_in'),
            array('row_cat_ex_ship_in'),
            array('total_cat_ex_ship_in'),
            array('unit_cat_in_ship_ex'),
            array('row_cat_in_ship_ex'),
            array('total_cat_in_ship_ex'),
            array('unit_cat_in_ship_in'),
            array('row_cat_in_ship_in'),
            array('total_cat_in_ship_in')
        );
    }



























//
//    /**
//     * Validate Order on the backend and validate prices with taxes
//     *
//     * @depends createSimpleProduct
//     * @depends createVirtualProduct
//     * @depends createOrderBackend
//     * @test
//     */
//    public function validateOrderBackend($simpleProductData, $virtualProductData, $orderId)
//    {
//        //Data
//        $verifyProducts = $this->loadData('validate_prices_in_order_review');
//        $verifyProducts['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProducts['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotal = $this->loadData('total_verification_order_review');
//
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
//        $this->searchAndOpen(array('filter_id' => $orderId));
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProducts, $verifyTotal);
//
//    }
//    /**
//     * Create Invoice on the backend and validate prices with taxes
//     *
//     * @depends createSimpleProduct
//     * @depends createVirtualProduct
//     * @depends createOrderBackend
//     * @test
//     */
//    public function createInvoiceBackend($simpleProductData, $virtualProductData, $orderId)
//    {
//        //Data
//        $verifyProductsBeforeInvoice = $this->loadData('validate_prices_in_invoice_creation');
//        $verifyProductsBeforeInvoice['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsBeforeInvoice['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalBeforeInvoice = $this->loadData('total_verification_invoice_creation');
//        $verifyProductsAfterInvoice = $this->loadData('validate_prices_after_invoice_creation');
//        $verifyProductsAfterInvoice['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsAfterInvoice['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalAfterInvoice = $this->loadData('total_verification_after_invoice_creation');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
//        $this->searchAndOpen(array('filter_id' => $orderId));
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//        $this->clickButton('invoice');
//        $this->assertTrue($this->checkCurrentPage('create_invoice'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsBeforeInvoice, $verifyTotalBeforeInvoice);
//        $this->clickButton('submit_invoice');
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsAfterInvoice, $verifyTotalAfterInvoice);
//    }
//
//    /**
//     * Review Invoice on the backend and validate prices with taxes
//     *
//     * @depends createSimpleProduct
//     * @depends createVirtualProduct
//     * @depends createOrderBackend
//     * @test
//     */
//    public function validateInvoiceBackend($simpleProductData, $virtualProductData, $orderId)
//    {
//        //Data
//        $verifyProductsInvoice = $this->loadData('validate_prices_invoice');
//        $verifyProductsInvoice['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsInvoice['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalInvoice = $this->loadData('total_verification_invoice');
//        //Steps
//        $this->navigate('manage_sales_invoices');
//        $this->assertTrue($this->checkCurrentPage('manage_sales_invoices'), $this->messages);
//        $this->searchAndOpen(array('filter_order_id' => $orderId));
//        $this->assertTrue($this->checkCurrentPage('view_invoice'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsInvoice, $verifyTotalInvoice);
//    }
//
//    /**
//     * Create Shipment on the backend and validate prices with taxes
//     *
//     * @depends createSimpleProduct
//     * @depends createVirtualProduct
//     * @depends createOrderBackend
//     * @test
//     */
//    public function createShipmentBackend($simpleProductData, $virtualProductData, $orderId)
//    {
//        //Data
//        $verifyProductsAfterShipment = $this->loadData('validate_prices_after_shipment_on_order_page');
//        $verifyProductsAfterShipment['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsAfterShipment['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalAfterShipment = $this->loadData('total_verification_after_shipment_on_order_page');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
//        $this->searchAndOpen(array('filter_id' => $orderId));
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//        $this->clickButton('ship');
//        $this->assertTrue($this->checkCurrentPage('create_shipment'), $this->messages);
//        $this->clickButton('submit_shipment');
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsAfterShipment, $verifyTotalAfterShipment);
//    }
//
//    /**
//     * Create Refund on the backend and validate prices with taxes
//     *
//     * @depends createSimpleProduct
//     * @depends createVirtualProduct
//     * @depends createOrderBackend
//     * @test
//     */
//    public function createRefundBackend($simpleProductData, $virtualProductData, $orderId)
//    {
//        //Data
//        $verifyProductsBeforeRefund = $this->loadData('validate_prices_before_refund_on_refund_page');
//        $verifyProductsBeforeRefund['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsBeforeRefund['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalBeforeRefund = $this->loadData('total_verification_before_refund_on_refund_page');
//        $verifyProductsAfterRefund = $this->loadData('validate_prices_after_refund_on_order_page');
//        $verifyProductsAfterRefund['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsAfterRefund['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalAfterRefund = $this->loadData('total_verification_after_refund_on_order_page');
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
//        $this->searchAndOpen(array('filter_id' => $orderId));
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//        $this->clickButton('credit_memo');
//        $this->assertTrue($this->checkCurrentPage('create_credit_memo'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsBeforeRefund, $verifyTotalBeforeRefund);
//        $this->clickButton('refund_offline');
//        $this->assertTrue($this->checkCurrentPage('view_order'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsAfterRefund, $verifyTotalAfterRefund);
//
//    }
//
//    /**
//     * Review Refund on the backend and validate prices with taxes
//     *
//     * @depends createSimpleProduct
//     * @depends createVirtualProduct
//     * @depends createOrderBackend
//     * @test
//     */
//    public function validateRefundBackend($simpleProductData, $virtualProductData, $orderId)
//    {
//        //Data
//        $verifyProductsRefund = $this->loadData('validate_prices_after_refund_on_refund_page');
//        $verifyProductsRefund['product_1']['product'] = $simpleProductData['general_name'];
//        $verifyProductsRefund['product_2']['product'] = $virtualProductData['general_name'];
//        $verifyTotalRefund = $this->loadData('total_verification_after_refund_on_refund_page');
//        //Steps
//        $this->navigate('manage_sales_creditmemos');
//        $this->assertTrue($this->checkCurrentPage('manage_sales_creditmemos'), $this->messages);
//        $this->searchAndOpen(array('filter_order_id' => $orderId));
//        $this->assertTrue($this->checkCurrentPage('view_credit_memo'), $this->messages);
//        $this->shoppingCartHelper()->verifyPricesDataOnPage($verifyProductsRefund, $verifyTotalRefund);
//    }
}
