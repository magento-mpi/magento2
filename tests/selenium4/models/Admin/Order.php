<?php
/**
 * Admin_Scope_Store model
 *
 * @author Magento Inc.
 */
class Model_Admin_Order extends Model_Admin {
    /**
     * creating an array containing the order status
     */
    var $orderStatus = array (
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

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->storeData = array(
            'email'          => Core::getEnvConfig('backend/customer/email'),
            'name'           => Core::getEnvConfig('backend/scope/storeview/name'),
            'productSku'     => Core::getEnvConfig('backend/createproduct/sku'),
            'shippingMethod' => Core::getEnvConfig('backend/shipping_method/flat_rate'),
            'paymentMethod'  => Core::getEnvConfig('backend/payment_method/check'),
        );
    }

    /**
     * Crete new order for user $email and StoreView $name using product $sku.
     * @param array $params May contain the following params:
     * email, name, productSku, shippingMethod, paymentMethod
     */
    public function doCreateOrder($params = array())
    {
        $result = true;
        $storeData = $params ? $params : $this->storeData;

	// Open Order Page
	$this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
	// Create new Order
	$this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/manage_orders/buttons/create_order"));

        $this->setUiNamespace('admin/pages/sales/orders/create_order/salect_user_for_order');

        // Fill fields Email and StoreView for searching user
	$this->type($this->getUiElement("inputs/search_by_user_email"),$storeData['email']);
	$this->type($this->getUiElement("inputs/search_by_store_view_name"),$storeData['name']);
	// Searching and selecting user
	$this->click($this->getUiElement("buttons/user_search"));
	$this->model->pleaseWait();
	// check whether user exists
	if ($this->isElementPresent($this->getUiElement("locators/select_user",$storeData['email']))) {
            $this->click($this->getUiElement("locators/select_user",$storeData['email']));
            $this->model->pleaseWait();
	} else {
            $this->setVerificationErrors("User does not exist");
            $result = false;
	}

        $this->setUiNamespace('admin/pages/sales/orders/create_order/select_sore_view_for_order');

	//selecting Store View
	if (!$this->waitForElement($this->getUiElement("page_elements/loaded_store_view"),10)) {
            $this->setVerificationErrors("No loaded Store View page");
            $result = false;
	}
	$this->click($this->getUiElement("locators/select_store_view",$storeData['name']));
	$this->model->pleaseWait();

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
	$this->type($this->getUiElement("input/search_by_product_sku"),$storeData['productSku']);
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
	if ($this->isElementPresent($storeData['paymentMethod'])) {
            $this->click($storeData['paymentMethod']);
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
	if (!$this->isElementPresent($storeData['shippingMethod'])) {
            $this->setVerificationErrors("This shipping method is currently unavailable.");
            $result = false;
	} else {
            $this->click($storeData['shippingMethod']);
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
        if ($result) {
            $this->printInfo('Order created');
        }
        return $result;
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

        $this->setUiNamespace('admin/pages/sales/orders/create_order/manage_orders');

        $this->type($this->getUiElement("input/order_number", $ordNum));
        $this->click($this->getUiElement("buttons/order_search"));
        $this->model->pleaseWait();
	if ($this->isElementPresent($this->getUiElement("locators/select_odrer", $ordNum))){
            $this->clickAndWait($this->getUiElement("locators/select_odrer", $ordNum));
        } else {
            $this->setVerificationErrors("Order with number $ordNum does not exist");
            $result = false;            
        }
        if ($result) {
            $this->printInfo('Order opened');
        }
        return $result;
    }

    /**
     * Create invoice for order.
     *@param 
     */
    public function doCreateInvoice()
    {
        $result = true;

        $this->setUiNamespace('admin/pages/sales/orders/edit_order');

        if (!$this->isElementPresent($this->getUiElement("buttons/create_invoice"))){
            $this->setVerificationErrors("You cannot create an Invoice for this order");
            $result = false;
	} else {
            $this->clickAndWait($this->getUiElement("buttons/create_invioce"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_invoice/buttons/sumbit_invoice"));
	}
	if (!$this->waitForElement($this->getUiElement("messages/invoice_created"),10)) {
            $this->setVerificationErrors("No success message about Invoice creation");
            $result = false;
	}
        if ($result) {
            $this->printInfo('Invoice created');
        }
        return $result;
    }

    /**
     * Create shippment for order.
     *@param
     */
    public function doCreateShippment()
    {
        $result = true;

        $this->setUiNamespace('admin/pages/sales/orders/edit_order');

        if (!$this->isElementPresent($this->getUiElement("buttons/create_ship"))){
            $this->setVerificationErrors("You cannot create a Shippment for this order");
            $result = false;
	} else {
            $this->clickAndWait($this->getUiElement("buttons/create_ship"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_shippment/buttons/sumbit_ship"));
	}
	if (!$this->isElementPresent($this->getUiElement("messages/ship_created"))) {
            $this->setVerificationErrors("No success message about Shippment creation");
            $result = false;
	}
        if ($result) {
            $this->printInfo('Shippment created');
        }
        return $result;
    }

    /**
     * Create Credit Memo for order.
     *@param
     */
    public function doCreateCreditMemo()
    {
        $result = true;

        $this->setUiNamespace('admin/pages/sales/orders/edit_order');

        if (!$this->isElementPresent($this->getUiElement("buttons/create_memo"))){
            $this->setVerificationErrors("You cannot create a Credit Memo for this order");
            $result = false;
	} else {
            $this->clickAndWait($this->getUiElement("buttons/create_memo"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_credit_memoe/buttons/sumbit_memo"));
	}
	if (!$this->isElementPresent($this->getUiElement("messages/c_memo_created"))) {
            $this->setVerificationErrors("No success message about Credit Memo creation");
            $result = false;
	}
        if ($result) {
            $this->printInfo('Credit Memo created');
        }
        return $result;
    }

    /**
     * Reorder for order.
     *@param
     */
    public function doReOrder()
    {
        $result = true;

        $this->setUiNamespace('admin/pages/sales/orders/edit_order');

	if (!$this->isElementPresent($this->getUiElement("buttons/reorder"),10)){
            $this->setVerificationErrors("You cannot reOrder this order");
            $result = false;
	} else {
            $this->clickAndWait($this->getUiElement("buttons/reorder"));
            $this->clickAndWait($this->getUiElement("/admin/pages/sales/orders/create_order/buttons/sumbit_order"));
	}
	if (!$this->waitForElement($this->getUiElement("/admin/pages/sales/orders/create_orderPage/messages/order_created"),10)) {
            $this->setVerificationErrors("No success message about ReOrder");
            $result = false;
	}
        if ($result) {
            $this->printInfo('ReOrder finished');
        }
        return $result;
    }

    /**
     * Defining the status for an order.
     *@param
     */
    public function orderStatus($ordNum, $orderStatus)
    {
        $result = true;

        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');

	$status = $this->getText($this->getUiElement("page_elements/order_status",$ordNum));
	if ($status != $orderStatus) {
            $this->setVerificationErrors("Order has an incorrect status after creation order.The order status is $status, but must have the status $orderStatus");
            $result = false;
	}
        if ($result) {
            $this->printInfo('order status is correct ');
        }
        return $result;
    }

}