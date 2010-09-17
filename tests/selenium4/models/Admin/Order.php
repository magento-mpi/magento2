<?php
/**
 * Admin_Admin_Order model
 *
 * @author Magento Inc.
 */
class Model_Admin_Order extends Model_Admin {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->orderData = array(
            'email'          => Core::getEnvConfig('backend/customer/email'),
            'name'           => Core::getEnvConfig('backend/scope/storeview/name'),
            'productSku'     => Core::getEnvConfig('backend/createproduct/sku'),
            'shippingMethod' => Core::getEnvConfig('backend/shipping_method/flat_rate'),
            'paymentMethod'  => Core::getEnvConfig('backend/payment_method/check'),
            'number'         => 200000041,
        );
        /**
         * an array containing the order status
         */
        $this->orderStatus = array (
            1  => "Pending",
            2  => "Pending Payment",
            3  => "Processing",
            4  => "On Hold",
            5  => "Complete",
            6  => "Closed",
            7  => "Canceled",
            8  => "Suspected Fraud",
            9  => "Payment Review",
            10 => "Pending PayPal",
            11 => "Pending Ogone",
            12 => "Cancelled Ogone",
            13 => "Declined Ogone",
            14 => "Processing Ogone Payment",
            15 => "Processed Ogone Payment",
            16 => "Waiting Authorization"
        );
    }

    /**
     * Crete new order for user  with 'email' and StoreView 'name' using product with sku 'productSku'.
     * @param array $params May contain the following params:
     * email, name, productSku, shippingMethod, paymentMethod
     */
    public function doCreateOrder($params = array(),$par = array())
    {
        //global $ordNum;
        $result = true;
        $orderData = $params ? $params : $this->orderData;
        $orderStatus = $par ? $par : $this->orderStatus;
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        // Create new Order
        $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/manage_orders/buttons/create_order"));
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/create_order/salect_user_for_order');
        // Fill fields Email and StoreView for searching user
        $this->type($this->getUiElement("inputs/search_by_user_email"),$orderData['email']);
        $this->type($this->getUiElement("inputs/search_by_store_view_name"),$orderData['name']);
        // Searching and selecting user
        $this->click($this->getUiElement("buttons/user_search"));
        $this->pleaseWait();
        // check whether user exists
        if ($this->waitForElement($this->getUiElement("locators/select_user",$orderData['email']),10)) {
            $this->click($this->getUiElement("locators/select_user",$orderData['email']));
            $this->model->pleaseWait();
        } else {
            $this->setVerificationErrors("User does not exist");
            $result = false;
            //die;
        }
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/create_order/select_sore_view_for_order');
        //selecting Store View
        if (!$this->waitForElement($this->getUiElement("page_elements/loaded_store_view"),10)) {
            $this->setVerificationErrors("No loaded Store View page");
            $result = false;
        }
        $this->click($this->getUiElement("locators/select_store_view",$orderData['name']));
        $this->model->pleaseWait();
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/create_order');
        if (!$this->waitForElement($this->getUiElement("page_elements/order_page"),10)) {
            $this->setVerificationErrors("No loaded Order Creation page");
            $result = false;
        }
        // Search and add product
        $this->click($this->getUiElement("buttons/add_product"));
        if (!$this->waitForElement($this->getUiElement("page_elements/product_search_grid"),10)) {
            $this->setVerificationErrors("No loaded Product Search Grid");
            $result = false;
        }
        $this->type($this->getUiElement("input/search_by_product_sku"),$orderData['productSku']);
        $this->click($this->getUiElement("buttons/product_search"));
        $this->model->pleaseWait();
        $productText = $this->getText($this->getUiElement("locators/select_product"));
        if ($productText != 'No records found.') {
            $this->click($this->getUiElement("locators/select_product"));
            $this->click($this->getUiElement("buttons/add_product_confirm"));
            $this->model->pleaseWait();
        } else {
            $this->setVerificationErrors("Product does not exist");
            $result = false;
        }
        // Select Payment Method (Check / Money order)
        if ($this->isElementPresent($orderData['paymentMethod'])) {
            $this->click($orderData['paymentMethod']);
            $this->model->pleaseWait();
        } else {
            $this->setVerificationErrors("This Payment method is currently unavailable.");
            $result = false;
        }
        // Select Shhiping Method (Flat Rate)
        $this->click($this->getUiElement("locators/get_shipping"));
        $this->model->pleaseWait();
        if ($this->isElementPresent($this->getUiElement("messages/verify_shipping"),10)) {
            $this->setVerificationErrors("Shipping Address is empty(or no Shipping Methods are available)");
            $result = false;
        } else {
        if (!$this->isElementPresent($orderData['shippingMethod'])) {
            $this->setVerificationErrors("This shipping method is currently unavailable.");
            $result = false;
        } else {
            $this->click($orderData['shippingMethod']);
            $this->model->pleaseWait();
        }
        }
        // Sumbit Order
        $this->clickAndWait($this->getUiElement("buttons/sumbit_order"));
        if ($this->isElementPresent($this->getUiElement("messages/verify_req_field"))) {
            $this->setVerificationErrors("Required field(s) empty");
            $result = false;
        } else {
        if ($this->isElementPresent($this->getUiElement("messages/order_not_created"))) {
            $etext = $this->getText($this->getUiElement("messages/order_not_created"));
            $this->setVerificationErrors("$etext");
            $result = false;
        } else {
        if (!$this->waitForElement($this->getUiElement("messages/order_created"),10)) {
            $this->setVerificationErrors("No success message about order creation");
            $result = false;
        }
        }
        }
        //Definition of order number
        $ordNum = $this->getText($this->getUiElement("/admin/pages/sales/orders/edit_order/page_elements/order_number"));
        $ordNum = substr($ordNum, 8, 10);
        if ($result) {
            $this->printInfo('Order created with number #'.$ordNum);
        }
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        //receiving and checking the status of order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $status = $this->getText($this->getUiElement("page_elements/order_status",$ordNum));
        if ($status != $orderStatus[1]) {
            $this->setVerificationErrors(
                    "Order has an incorrect status.The order status is $status,but must have the status $orderStatus[1]"
            );
            $result = false;
        }
        if ($result) {
            $this->printInfo('After creation order: order status is correct');
        }
        return $ordNum;
    }

    /**
     * Open order with number $ordNum.
     *@param $ordNum
     */
    public function doOpenOrder($ordNum)
    {
        $result = true;

        // Open Order Page and Order
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $this->click($this->getUiElement("buttons/reset_search"));
        $this->pleaseWait();
        $this->type($this->getUiElement("input/order_number"),$ordNum);
        $this->click($this->getUiElement("buttons/order_search"));
        $this->pleaseWait();
        if ($this->waitForElement($this->getUiElement("locators/select_order",$ordNum),10)) {
            $this->clickAndWait($this->getUiElement("locators/select_order",$ordNum));
        } else {
          $this->setVerificationErrors("Order with number #".$ordNum." does not exist");
          $result = false;
        }
        if ($result) {
            $this->printInfo('Order opened');
        }
    }

    /**
     * Create invoice for order.
     *@param 
     */
    public function doCreateInvoice($ordNum)
    {
        $par = array();
        $result = true;
        $orderStatus = $par ? $par : $this->orderStatus;
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/edit_order');
        //checking: can create an Invoice?
        if (!$this->waitForElement($this->getUiElement("buttons/create_invoice"),10)) {
            $this->setVerificationErrors("You cannot create an Invoice for this order");
            $result = false;
        } else {
            $this->clickAndWait($this->getUiElement("buttons/create_invoice"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_invoice/buttons/sumbit_invoice"));
        if (!$this->waitForElement($this->getUiElement("messages/invoice_created"),10)) {
            $this->setVerificationErrors("No success message about Invoice creation");
            $result = false;
        }
        }
        if ($result) {
            $this->printInfo('Invoice created');
        }
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        //receiving and checking the status of order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $status = $this->getText($this->getUiElement("page_elements/order_status",$ordNum));
        if ($status != $orderStatus[3]) {
        $this->setVerificationErrors(
                    "Order has an incorrect status.The order status is $status,but must have the status $orderStatus[1]"
            );
            $result = false;
        }
        if ($result) {
            $this->printInfo('After creation an invoice: order status is correct');
        }
        return $ordNum;
    }

    /**
     * Create shippment for order.
     *@param
     */
    public function doCreateShippment($ordNum)
    {
        $par = array();
        $result = true;
        $orderStatus = $par ? $par : $this->orderStatus;
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/edit_order');
        //checking: can create a Shippment?
        if (!$this->isElementPresent($this->getUiElement("buttons/create_ship"))){
            $this->setVerificationErrors("You cannot create a Shippment for this order");
            $result = false;
        } else {
            $this->clickAndWait($this->getUiElement("buttons/create_ship"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_shippment/buttons/sumbit_ship"));
        if (!$this->isElementPresent($this->getUiElement("messages/ship_created"))) {
            $this->setVerificationErrors("No success message about Shippment creation");
            $result = false;
        }
        }        
        if ($result) {
            $this->printInfo('Shippment created');
        }
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        //receiving and checking the status of order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $status = $this->getText($this->getUiElement("page_elements/order_status",$ordNum));
        if ($status != $orderStatus[5]) {
            $this->setVerificationErrors(
                    "Order has an incorrect status.The order status is $status,but must have the status $orderStatus[1]"
            );
            $result = false;
        }
        if ($result) {
            $this->printInfo('After creation Shippment: order status is correct');
        }
        return $ordNum;
        }

    /**
     * Create Credit Memo for order.
     *@param
     */
    public function doCreateCreditMemo($ordNum)
    {
        $par = array();
        $result = true;
        $orderStatus = $par ? $par : $this->orderStatus;
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/edit_order');
        //checking: can create a Credit Memo?
        if (!$this->isElementPresent($this->getUiElement("buttons/create_memo"))){
            $this->setVerificationErrors("You cannot create a Credit Memo for this order");
            $result = false;
        } else {
            $this->clickAndWait($this->getUiElement("buttons/create_memo"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_credit_memo/buttons/sumbit_memo"));
        if (!$this->isElementPresent($this->getUiElement("messages/c_memo_created"))) {
            $this->setVerificationErrors("No success message about Credit Memo creation");
            $result = false;
        }
        }        
        if ($result) {
            $this->printInfo('Credit Memo created');
        }
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        //receiving and checking the status of order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $status = $this->getText($this->getUiElement("page_elements/order_status",$ordNum));
        if ($status != $orderStatus[6]) {
            $this->setVerificationErrors(
                    "Order has an incorrect status.The order status is $status,but must have the status $orderStatus[1]"
            );
            $result = false;
        }
        if ($result) {
            $this->printInfo('After creation credit memo: order status is correct');
        }
        return $ordNum;
    }

    /**
     * Reorder for order.
     *@param
     */
    public function doReOrder($par = array())
    {
        $result = true;
        $orderStatus = $par ? $par : $this->orderStatus;
        // set UiNamespace
        $this->setUiNamespace('admin/pages/sales/orders/edit_order');
        //checking: can reorder a Order?
        if (!$this->isElementPresent($this->getUiElement("buttons/reorder"),10)){
            $this->setVerificationErrors("You cannot reOrder this order");
            $result = false;
        } else {
            $this->clickAndWait($this->getUiElement("buttons/reorder"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_order/buttons/sumbit_order"));
        if (!$this->waitForElement($this->getUiElement("/admin/pages/sales/orders/create_order/messages/order_created"),20)) {
            $this->setVerificationErrors("No success message about ReOrder");
            $result = false;
        }
        }
        //Definition of order number
        $ordNum = $this->getText($this->getUiElement("/admin/pages/sales/orders/edit_order/page_elements/order_number"));
        $ordNum = substr($ordNum, 8, 10);
        if ($result) {
            $this->printInfo('ReOrder finished');
        }
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        //Search Order
        $this->type($this->getUiElement("/admin/pages/sales/orders/manage_orders/input/order_number"),$ordNum);
        $this->click($this->getUiElement("/admin/pages/sales/orders/manage_orders/buttons/order_search"));
        $this->pleaseWait();
        //receiving and checking the status of order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $status = $this->getText($this->getUiElement("page_elements/order_status",$ordNum));
        if ($status != $orderStatus[1]) {
            $this->setVerificationErrors(
                    "Order has an incorrect status.The order status is $status,but must have the status $orderStatus[1]"
            );
            $result = false;
        }
        if ($result) {
            $this->printInfo('After reorder: order status is correct');
        }
    }
}