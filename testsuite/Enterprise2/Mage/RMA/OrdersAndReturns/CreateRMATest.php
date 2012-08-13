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
 * Orders And Returns tests
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_RMA_createRMATest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('RMA/enable_rma_on_frontend');
    }

    /**
     * Create Products
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple1 = $this->loadDataSet('Product', 'simple_product_visible');
        $simple2 = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($simple2);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('products' => array ('simple1'  => array ('name' => $simple1['general_name'],
                                                               'sku'  => $simple1['general_sku']),
                                          'simple2'  => array ('name' => $simple2['general_name']),
                                                               'sku'  => $simple2['general_sku']));
    }

    /**
     * <p> Enable RMA on frontend</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with simple product as a guest</p>
     * <p>4. Create shipment for order</p>
     * <p>Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Click "Return" link</p>
     * <p>Expected result</p>
     * <p>1. "Create New Return" page is open</p>
     * <p>2. Page contain: Contact Email Address, Item, Quantity To Return, Resolution, Item Condition, Reason to Return, Comments fields and  Add Item To Return link</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6044
     */
    public function enableRMA($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['products']['simple1']['name']));
        $this->addParameter('param', '0');
        //Preconditions
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        //Steps
        $this->addParameter('orderId', $orderNumber);
        $this->frontend('orders_and_returns');
        $this->fillFieldset($orderInfo, 'orders_and_returns_form');
        $this->clickButton('continue');
        $this->clickControl('link', 'return');
        //Verification
        $this->validatePage('guest_create_rma');
        $this->assertTrue($this->controlIsPresent('field', 'email'),
            'There is no "Contact Email Address" field on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'item'), 'There is no "Item" field on the page');
        $this->assertTrue($this->controlIsPresent('field', 'quantity'),
            'There is no "Quantity To Return" field on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'resolution'),
            'There is no "Resolution" field on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'condition'),
            'There is no "Condition" field on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'reason'),
            'There is no "Reason to Return" field on the page');
        $this->assertTrue($this->controlIsPresent('field', 'comment'), 'There is no "Comments" field on the page');
        $this->assertTrue($this->controlIsPresent('link', 'add_item_to_return'),
            'There is no "Add Item To Return" link on the page');
        $this->assertTrue($this->controlIsPresent('button', 'submit'), 'There is no "Submit" button on the page');
    }

    /**
     * <p> Create RMA with Simple product</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with simple product as a guest</p>
     * <p>4. Create shipment for order</p>
     * <p>Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Click "Return" link</p>
     * <p>6. Fill all required field in return form</p>
     * <p>7. Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>1. "Guest Returns" page is open</p>
     * <p>2. Show message "Return # has been submitted successfully" </p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6045
     */
    public function returnSimpleProduct($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['products']['simple1']['name']));
        $rmaData = $this->loadDataSet('RMA', 'rma_request', array('item' => $testData['products']['simple1']['name']));
        //Preconditions
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->frontend();
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData);
        //Verification
        $this->validatePage('guest_view_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Return wrong product quantity</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with simple product as a guest</p>
     * <p>4. Create shipment for order</p>
     * <p>Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Click "Return" link</p>
     * <p>6. Set product quantity 2 and fill all required field in return form</p>
     * <p>7. Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>1. Show message "Quantity of <Product name> is greater than you can return." </p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6046
     */
    public function returnWrongQuantity($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['products']['simple1']['name']));
        $rmaData = $this->loadDataSet('RMA', 'rma_request',
            array('item' => $testData['products']['simple1']['name'], 'quantity' => '2'));
        //Preconditions
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->frontend();
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData);
        //Verification
        $this->validatePage('guest_create_rma');
        $this->assertMessagePresent('error', 'specify_product_quantity');
    }

    /**
     * <p> Create RMA for several products</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with two different simple product as a guest</p>
     * <p>4. Create shipment for order</p>
     * <p>Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Click "Return" link</p>
     * <p>6. Select one Simple product in Item field and fill all required field in return form</p>
     * <p>7. Click "Add item to return" link</p>
     * <p>8. Select other Product in new Item field and fill all required field in return form</p>
     * <p>9. Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>1. "Guest Returns" page is open</p>
     * <p>2. Show message "Return # has been submitted successfully" </p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6047
     */
    public function severalProducts($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney');
        $checkoutData['products_to_add']['product_1']['general_name'] = $testData['products']['simple1']['name'];
        $checkoutData['products_to_add']['product_2']['general_name'] = $testData['products']['simple2']['name'];
        $rmaData = $this->loadDataSet('RMA', 'return_two_products');
        $rmaData['rma_1']['item'] = $testData['products']['simple1']['name'];
        $rmaData['rma_2']['item'] = $testData['products']['simple2']['name'];
        //Preconditions
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->frontend();
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData);
        //Verification
        $this->validatePage('guest_view_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Create several RMA for one order</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with Two different Simple products as a guest</p>
     * <p>4. Create shipment for order</p>
     * <p>Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Click "Return" link</p>
     * <p>5. Create RMA only for One product</p>
     * <p>6. Create RMA  for other product<</p>
     * <p>Expected result</p>
     * <p>1. "Guest Returns" page is open</p>
     * <p>2. Show message "Return # has been submitted successfully" </p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6048
     */
    public function severalReturnForOneOrder($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney');
        $checkoutData['products_to_add']['product_1']['general_name'] = $testData['products']['simple1']['name'];
        $checkoutData['products_to_add']['product_2']['general_name'] = $testData['products']['simple2']['name'];
        $rmaData1 = $this->loadDataSet('RMA', 'rma_request');
        $rmaData1['rma_1']['item'] = $testData['products']['simple1']['name'];
        $rmaData2 = $this->loadDataSet('RMA', 'rma_request');
        $rmaData2['rma_1']['item'] = $testData['products']['simple2']['name'];
        //Preconditions
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->frontend();
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData1);
        $this->validatePage('guest_view_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData2);
        //Verification
        $this->validatePage('guest_view_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p> Create RMA after partial shipment</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order as a guest with simple product and qty=5 </p>
     * <p>4. Create partial shipment for order: product qty = 3</p>
     * <p> Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Click "Return" link</p>
     * <p>6. Try create return for 5 product</p>
     * <p>7. Create return for 3 product</p>
     * <p>Expected result</p>
     * <p>1. After 5 show message "Quantity of %productName% is greater than you can return."</p>
     * <p>3. After 6 "Guest Returns" page is open</p>
     * <p>4. After 6 Show message "Return # has been submitted successfully" </p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6050
     */
    public function returnAfterPartialShipment($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney');
        $rmaData1 = $this->loadDataSet('RMA', 'rma_request',
            array('item' => $testData['products']['simple1']['name'], 'quantity' => '5'));
        $rmaData2 = $this->loadDataSet('RMA', 'rma_request',
            array('item' => $testData['products']['simple1']['name'], 'quantity' => '3'));
        //Preconditions
        $this->productHelper()->frontOpenProduct($testData['products']['simple1']['name']);
        $this->fillField('product_qty', '5');
        $this->productHelper()->frontAddProductToCart();
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        $shipmentData = array('ship_product_sku' => $testData['products']['simple1']['sku'], 'ship_product_qty' => '3');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()
            ->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber), array('shipment' => $shipmentData));
        //Steps
        $this->frontend();
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData1);
        $this->validatePage('guest_create_rma');
        $this->assertMessagePresent('error', 'specify_product_quantity');
        $this->RMAHelper()->frontGuestCreateRMA($orderInfo, $rmaData2);
        //Verification
        $this->validatePage('guest_view_returns');
        $this->assertMessagePresent('success', 'successfully_submitted_return');
    }

    /**
     * <p>Create RMA for order without shipment</p>
     * <p>Preconditions</p>
     * <p>1. Enable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with simple product as a guest</p>
     * <p>Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>Expected result</p>
     * <p>1. "Return" link is missing</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @depends enableRMA
     * @test
     * @TestlinkId TL-MAGE-6052
     */
    public function withoutShipment($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['products']['simple1']['name']));
        //Preconditions
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        //Steps
        $this->addParameter('orderId', $orderNumber);
        $this->frontend('orders_and_returns');
        $this->fillFieldset($orderInfo, 'orders_and_returns_form');
        $this->clickButton('continue');
        //Verification
        $this->assertFalse($this->controlIsPresent('link', 'return'), 'Return link must be absent');
    }

    /**
     * <p> Disable RMA on frontend</p>
     * <p>Preconditions</p>
     * <p>1. Disable RMA on frontend (System-Configuration-Sales-Sales-RMA Settings)</p>
     * <p>2. Open frontend</p>
     * <p>3. Place order with Two different Simple products as a guest</p>
     * <p>4. Create shipment for order</p>
     * <p> Steps</p>
     * <p>1. Open frontend</p>
     * <p>2. Click "Orders and Returns" link in pages footer </p>
     * <p>3. Fill all required fields </p>
     * <p>4. Click "Continue" button</p>
     * <p>Expected result</p>
     * <p>1. "Return" link is missing</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6051
     */
    public function disableRMA($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['products']['simple1']['name']));
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('RMA/disable_rma_on_frontend');
        $this->frontend();
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $orderInfo = array ('order_id' => $orderNumber,
                            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                            'search_type_id' => 'Email Address',
                            'email' => $checkoutData['billing_address_data']['billing_email']);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderNumber));
        //Steps
        $this->addParameter('orderId', $orderNumber);
        $this->frontend('orders_and_returns');
        $this->fillFieldset($orderInfo, 'orders_and_returns_form');
        $this->clickButton('continue');
        //Verification
        $this->assertFalse($this->controlIsPresent('link', 'return'), 'Return link must be absent');
    }
}
