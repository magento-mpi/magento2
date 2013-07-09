<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_SalesOrderActionsTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTestsCreateProduct()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address', array(
            'default_billing_address' => 'Yes',
            'default_shipping_address' => 'Yes'
        ));
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

        return array('sku' => $simple['general_sku'], 'email' => $userData['email']);
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Create (+View) can create order</p>
     *
     * @param array $testData
     *
     * @depends preconditionsForTestsCreateProduct
     * @test
     * @TestlinkId TL-MAGE-5721
     */
    public function permissionCreateOrder($testData)
    {
        // Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-orders-actions-create', 'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical', array(
            'filter_sku' => $testData['sku'],
            'email' => $testData['email'],
            'customer_email' => '%noValue%',
            'billing_addr_data' => '%noValue%',
            'shipping_addr_data' => $this->loadDataSet('SalesOrder', 'shipping_address_same_as_blling')
        ));
        //Steps And Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->assertTrue($this->checkCurrentPage('view_order'), $this->getParsedMessages());
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'hold', 'unhold', 'credit_memo',
            'invoice', 'ship', 'reorder', 'void');
        $buttonsTrue = array('back');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Preconditions for next tests
     *
     * @param $testData
     *
     * @return string
     *
     * @depends preconditionsForTestsCreateProduct
     * @test
     */
    public function createOrderForTest($testData)
    {
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical', array(
            'filter_sku' => $testData['sku'],
            'email' => $testData['email'],
            'customer_email' => '%noValue%',
            'billing_addr_data' => '%noValue%',
            'shipping_addr_data' => $this->loadDataSet('SalesOrder', 'shipping_address_same_as_blling')
        ));
        //Steps And Verifying
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->assertTrue($this->checkCurrentPage('view_order'), $this->getParsedMessages());

        return $orderId;
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Invoice (+View, Invoices) can invoice order</p>
     *
     * @param string
     *
     * @depends createOrderForTest
     * @test
     * @TestlinkId TL-MAGE-5720
     */
    public function permissionInvoiceOrder($orderId)
    {
        // Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-invoices',
                'sales-operations-orders-actions-invoice',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'hold', 'unhold', 'credit_memo',
            'ship', 'reorder', 'void');
        $buttonsTrue = array('back', 'invoice');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Hold (+View) can Hold order</p>
     *
     * @param $orderId
     *
     * @depends createOrderForTest
     *
     * @test
     * @TestlinkId TL-MAGE-5722
     */
    public function permissionHoldOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-orders-actions-hold',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        //Steps And Verifying
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'ship', 'unhold', 'credit_memo', 'invoice',
            'reorder', 'void');
        $buttonsTrue = array('back', 'hold');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->clickButton('hold');
        $this->assertMessagePresent('success', 'success_hold_order');
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Unhold (+View) can Unhold order</p>
     *
     * @param $orderId
     *
     * @depends createOrderForTest
     * @depends permissionHoldOrder
     *
     * @test
     * @TestlinkId TL-MAGE-5723
     */

    public function permissionUnholdOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-orders-actions-unhold',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'ship', 'hold', 'credit_memo', 'invoice',
            'reorder', 'void');
        $buttonsTrue = array('back', 'unhold');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->clickButton('unhold');
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Ship (+View) can Ship order</p>
     *
     * @param $orderId
     *
     * @depends createOrderForTest
     * @depends permissionUnholdOrder
     *
     * @test
     * @TestlinkId TL-MAGE-5724
     */
    public function permissionShipOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-shipments',
                'sales-operations-orders-actions-ship',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Steps And Verifying
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'hold', 'unhold', 'credit_memo',
            'invoice', 'reorder', 'void');
        $buttonsTrue = array('back', 'ship');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Credit Memos</p>
     * <p>(+View,Credit Memos) can create Credit Memo for order</p>
     *
     * @param $orderId
     *
     * @depends createOrderForTest
     * @depends permissionShipOrder
     *
     * @test
     * @TestlinkId TL-MAGE-5726
     */
    public function permissionCreditMemoOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-credit_memos',
                'sales-operations-orders-actions-credit_memos',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        ////Steps And Verifying
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'hold', 'unhold', 'ship', 'invoice', 'reorder', 'void');
        $buttonsTrue = array('back', 'credit_memo');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Reorder (+View,Create) can Reorder order</p>
     *
     * @param $orderId
     *
     * @return string
     *
     * @depends createOrderForTest
     * @depends permissionCreditMemoOrder
     *
     * @test
     * @TestlinkId TL-MAGE-5725
     */
    public function permissionReorderOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-orders-actions-create',
                'sales-operations-orders-actions-reorder',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        ////Steps And Verifying
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('edit', 'cancel', 'send_email', 'hold', 'unhold', 'ship',
            'invoice', 'credit_memo', 'void');
        $buttonsTrue = array('back', 'reorder');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->clickButton('reorder');
        $orderId = $this->orderHelper()->submitOrder();
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderId;
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Edit (+View,Create) has ability to edit(create new) order</p>
     *
     * @param $orderId
     *
     * @return string
     *
     * @depends permissionReorderOrder
     * @test
     * @TestlinkId TL-MAGE-5727
     */
    public function permissionEditOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-orders-actions-create',
                'sales-operations-orders-actions-edit',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        ////Steps And Verifying
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('reorder', 'cancel', 'send_email', 'hold', 'unhold', 'ship',
            'invoice', 'credit_memo', 'void');
        $buttonsTrue = array('back', 'edit');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->clickButtonAndConfirm('edit', 'confirmation_for_edit');
        $orderId = $this->orderHelper()->submitOrder();
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderId;
    }

    /**
     * <p>Admin user with Role Sales/Orders/Actions/Cancel (+View) has ability to cancel order</p>
     *
     * @param $orderId
     *
     * @depends permissionEditOrder
     *
     * @test
     * @TestlinkId TL-MAGE-5728
     */
    public function permissionCancelOrder($orderId)
    {
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'sales-operations-orders-actions-cancel',
                'sales-operations-orders-actions-view'
            ))
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->getParsedMessages());
        ////Steps And Verifying
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $buttonsFalse = array('reorder', 'edit', 'send_email', 'hold', 'unhold', 'ship',
            'invoice', 'credit_memo', 'void');
        $buttonsTrue = array('back', 'cancel');
        foreach ($buttonsFalse as $button) {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page");
            }
        }
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }
}
