<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_RMA
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * RMA tests
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Rma_FrontendCreateRmaTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('RMA/enable_rma_on_frontend');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    /**
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $simple1 = $this->loadDataSet('Product', 'simple_product_visible');
        $simple2 = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($simple2);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'user' => array('email' => $userData['email'], 'password' => $userData['password']),
            'products' => array(
                'simple1' => array('name' => $simple1['general_name'], 'sku' => $simple1['general_sku']),
                'simple2' => array('name' => $simple2['general_name']),
                'sku' => $simple2['general_sku']
            )
        );
    }

    /**
     * <p> Enable RMA on frontend</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6008
     */
    public function enableRMA($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('general_name' => $testData['products']['simple1']['name']));
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->addParameter('orderId', $orderNumber);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('customer_account');
        $this->assertTrue($this->controlIsPresent('link', 'my_returns_tab'), 'My Returns tab must be present');
        $this->frontend('my_orders_history');
        $this->addParameter('elementTitle', $orderNumber);
        $this->clickControl('link', 'view_order');
        $this->assertTrue($this->controlIsPresent('link', 'return'), 'Return link must be present');
        $this->clickControl('link', 'return');
        //Verification
        $this->validatePage('create_new_return');
        $this->addParameter('param', 0);
        $this->assertTrue($this->controlIsPresent('field', 'email'), '"Email" field must be present');
        $this->assertTrue($this->controlIsPresent('dropdown', 'item'), '"Item" dropdown must be present');
        $this->assertTrue($this->controlIsPresent('field', 'quantity'), '"Quantity To Return" field must be present');
        $this->assertTrue($this->controlIsPresent('dropdown', 'resolution'), '"Resolution" dropdown must be present');
        $this->assertTrue($this->controlIsPresent('dropdown', 'condition'), '"Condition" dropdown must be present');
        $this->assertTrue($this->controlIsPresent('dropdown', 'reason'), '"Reason To Return" dropdown must be present');
        $this->assertTrue($this->controlIsPresent('field', 'comment'), '"Comments" field must be present');
        $this->assertTrue($this->controlIsPresent('link', 'add_item_to_return'),
            '"Add Item To Return" link must be present');
        $this->assertTrue($this->controlIsPresent('button', 'submit'), '"Submit" button must be present');
    }

    /**
     * <p> Create RMA with Simple product from Customer account</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6008
     */
    public function returnSimpleProduct($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('general_name' => $testData['products']['simple1']['name']));
        $rmaData = $this->loadDataSet('RMA', 'rma_request', array('item' => $testData['products']['simple1']['name']));
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData);
        //Verification
        $this->validatePage('my_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Return wrong product quantity from Customer account</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6009
     */
    public function returnWrongQuantity($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('general_name' => $testData['products']['simple1']['name']));
        $rmaData = $this->loadDataSet('RMA', 'rma_request',
            array('item' => $testData['products']['simple1']['name'], 'quantity' => '2'));
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData);
        //Verification
        $this->validatePage('create_new_return');
        $this->assertMessagePresent('error', 'specify_product_quantity');
    }

    /**
     * <p> Create RMA for several products from Customer account</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6010
     */
    public function severalProducts($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa');
        $checkoutData['products_to_add']['product_1']['general_name'] = $testData['products']['simple1']['name'];
        $checkoutData['products_to_add']['product_2']['general_name'] = $testData['products']['simple2']['name'];
        $rmaData = $this->loadDataSet('RMA', 'return_two_products');
        $rmaData['rma_1']['item'] = $testData['products']['simple1']['name'];
        $rmaData['rma_2']['item'] = $testData['products']['simple2']['name'];
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData);
        //Verification
        $this->validatePage('my_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Create several RMA for one order from Customer account</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6011
     */
    public function severalReturnForOneOrder($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa');
        $checkoutData['products_to_add']['product_1']['general_name'] = $testData['products']['simple1']['name'];
        $checkoutData['products_to_add']['product_2']['general_name'] = $testData['products']['simple2']['name'];
        $rmaData1 = $this->loadDataSet('RMA', 'rma_request');
        $rmaData1['rma_1']['item'] = $testData['products']['simple1']['name'];
        $rmaData2 = $this->loadDataSet('RMA', 'rma_request');
        $rmaData2['rma_1']['item'] = $testData['products']['simple2']['name'];
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData1);
        $this->validatePage('my_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData2);
        //Verification
        $this->validatePage('my_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Create RMA after partial shipment from Customer account</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6012
     */
    public function returnAfterPartialShipment($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa');
        $rmaData1 = $this->loadDataSet('RMA', 'rma_request',
            array('item' => $testData['products']['simple1']['name'], 'quantity' => '5'));
        $rmaData2 = $this->loadDataSet('RMA', 'rma_request',
            array('item' => $testData['products']['simple1']['name'], 'quantity' => '3'));
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($testData['products']['simple1']['name']);
        $this->fillField('product_qty', '5');
        $this->productHelper()->frontAddProductToCart();
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $shipmentData = array('ship_product_sku' => $testData['products']['simple1']['sku'], 'ship_product_qty' => '3');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()
            ->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber), array('shipment' => $shipmentData));
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData1);
        $this->validatePage('create_new_return');
        $this->assertMessagePresent('error', 'specify_product_quantity');
        $this->addParameter('elementTitle', $orderNumber);
        $this->rmaHelper()->frontCreateRMA($orderNumber, $rmaData2);
        //Verification
        $this->validatePage('my_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Create RMA without shipment from Customer account</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6053
     */
    public function withoutShipment($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('general_name' => $testData['products']['simple1']['name']));
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Steps
        $this->addParameter('orderId', $orderNumber);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
        $this->addParameter('elementTitle', $orderNumber);
        $this->clickControl('link', 'view_order');
        //Verification
        $this->assertFalse($this->controlIsPresent('link', 'return'), 'Return link must be absent');
    }

    /**
     * <p> Disable RMA on frontend</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6013
     */
    public function disableRMA($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('general_name' => $testData['products']['simple1']['name']));
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('RMA/disable_rma_on_frontend');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->addParameter('orderId', $orderNumber);
        $this->frontend('customer_account');
        $this->assertFalse($this->controlIsPresent('link', 'my_returns_tab'), 'My Returns tab must be absent');
        $this->frontend('my_orders_history');
        $this->addParameter('elementTitle', $orderNumber);
        $this->clickControl('link', 'view_order');
        //Verification
        $this->assertFalse($this->controlIsPresent('link', 'return'), 'Return link must be absent');
        $this->frontend('customer_account');
        $this->assertFalse($this->controlIsPresent('link', 'my_returns_tab'), 'My Returns tab must be absent');
    }
}
