<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_ACL_SalesOrderActionsOneRoleTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Precondition Method</p>
     * <p>Creating Simple product and test customer for next tests</p>
     *
     * @test
     *
     * @return array
     */
    public function preconditionsForTestsCreateProduct()
    {
        $this->loginAdminUser();
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('SalesOrderActions', 'customer_addresses');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('sku'   => $simple['general_name'], 'email' => $userData['email']);
    }

    /**
     * <p>Precondition Method</p>
     * <p>Create test Role with full permissions for Sales Order Actions</p>
     *
     * @depends
     * @test preconditionsForTestsCreateProduct
     * @return array
     */
    public function createRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Sales/Orders/Actions'));
        $roleSource['role_resources_tab']['role_resources']['resource_2'] = 'Sales/Invoices';
        $roleSource['role_resources_tab']['role_resources']['resource_3'] = 'Sales/Shipments';
        $roleSource['role_resources_tab']['role_resources']['resource_4'] = 'Sales/Credit Memos';
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');

        return $roleSource;
    }

    /**
     * <p>Precondition Method</p>
     * <p> Create test Admin user with test Role(Full permissions for order actions) </p>
     *
     * @param array $roleSource
     *
     * @depends createRole
     * @test
     * @return array
     *
     */
    public function createAdminWithTestRole($roleSource)
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return $testAdminUser;
    }

    /**
     * <p>Verify count of Top Navigation elements and they children</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Expected results:</p>
     * <p>3. Opened page is Sales Order page</p>
     * <p>Expected results: </p>
     * <p>1. Count of Top Navigation elements = 1</p>
     * <p>2. Count of Children Top Navigation elements = 4</p>
     *
     * @param array $testAdminUser
     * @depends createAdminWithTestRole
     * @test
     *
     * @TestlinkId TL-MAGE-5967
     */
    public function verifyScopeOneRole($testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        // Verify that navigation menu has only one element
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 4 child elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_children_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('4', count($navigationElements),
            'Count of child Navigation Menu not equal 4, should be equal 4');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create order </p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Create order(click "Creat New Order" and fill all required fields)</p>
     * <p>Expected results:</p>
     * <p>1. Sales Order page is opened</p>
     * <p>2. Order is created, success message is appeared</p>
     *
     * @param array $testAdminUser
     * @param array $orderData
     *
     * @depends createAdminWithTestRole
     * @depends preconditionsForTestsCreateProduct
     * @depends verifyScopeOneRole
     * @test
     * @return bool|int
     * @TestlinkId TL-MAGE-5968
     */
    public function createOrderOneRole($testAdminUser, $orderData)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->navigate('manage_sales_orders');
        $orderCreationData = $this->loadDataSet('SalesOrderActions', 'order_data',
            array('filter_sku' => $orderData['sku'], 'email'      => $orderData['email']));
        $this->orderHelper()->createOrder($orderCreationData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->validatePage('view_order');
        $orderId = $this->orderHelper()->defineOrderId();

        return $orderId;
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create invoice for order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Create invoice for this order(click "Invoice" button and do all required actions)</p>
     * <p>Expected results:</p>
     * <p>1. Invoice for test order is created, success message is appeared</p>
     *
     * @param string $orderId
     * @param array $testAdminUser
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5969
     */
    public function createInvoiceTestOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can Hold order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Put test order tu Hold status(click "Hold" button)</p>
     * <p>Expected results:</p>
     * <p>1. Test Order is held, success message is appeared</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5970
     */
    public function holdOrderOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->saveForm('hold');
        $this->assertMessagePresent('success', 'success_hold_order');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can unHold order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid "test order" and click</p>
     * <p>3. Put test order to unHold status(click "unHold" button)</p>
     * <p>Expected results:</p>
     * <p>1. Test Order is unHeld, success message is appeared</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5971
     */
    public function unHoldOrderOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->saveForm('unhold');
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create Shipment for order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Create Shipment for test order(click "Ship" button and fill all required fields)</p>
     * <p>Expected results:</p>
     * <p>1. Shipment for test order is created, success message is appeared</p>
     *
     * @param array $testAdminUser
     * @param $orderId
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5972
     */
    public function createShippingOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create Credit Memo for order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Create Credit Memo for test order(click "Credit Memo" button and fill all required fields)</p>
     * <p>Expected results:</p>
     * <p>1. "Credit Memo" for test order is created, success message is appeared</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5973
     */
    public function createCreditMemoOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create Reorder for order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Make Reorder for test order(click "Reorder" button and confirm)</p>
     * <p>Expected results:</p>
     * <p>1. New order based on test order is created, success message is appeared</p>
     *
     * @param bool|int $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @return bool|int
     * @TestlinkId TL-MAGE-5974
     */
    public function reorderOrderOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOrder();
        $orderId = $this->orderHelper()->defineOrderId();
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderId;
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can edit Order(create new)</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Make Edit for test order(click "Edit" button and confirm)</p>
     * <p>Expected results:</p>
     * <p>1. New order based on test order is created, success message is appeared</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends reorderOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @return bool|int
     * @TestlinkId TL-MAGE-5976
     */
    public function editOrderOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->clickButtonAndConfirm('edit', 'confirmation_for_edit');
        $this->orderHelper()->submitOrder();
        $orderId = $this->orderHelper()->defineOrderId();
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderId;
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can cancel order</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Search in orders grid test order and click</p>
     * <p>3. Make Cancel for test order(click "Cancel" button and confirm)</p>
     * <p>Expected results:</p>
     * <p>1. Test order is canceled, success message is appeared</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends editOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5975
     */
    public function cancelOrderOneRole($orderId, $testAdminUser)
    {
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->searchAndOpen(array('filter_order_id'=> $orderId), 'sales_order_grid');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }
}