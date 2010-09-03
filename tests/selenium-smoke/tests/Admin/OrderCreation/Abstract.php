<?php
/**
* Abstract test class for Admin/Order create module
*
* @author Magento Inc.
*/
abstract class Test_Admin_OrderCreation_Abstract extends Test_Admin_Abstract
{
	/**
	* Helper local instance
	*
	* @var Helper_Admin
	*/
	protected $_helper = null;

	/**
	* User Email
	*
	* @var string
	*/
	protected $_userEmail = '';

	/**
	* Product SKU
	*
	* @var string
	*/
	protected $_productSKU = '';

	/**
	* Order status
	*
	* @var array
	*/
	protected $_ordStatus = '';

	/**
	* Initialize the environment
	*/
	public function  setUp() {
		parent::setUp();
	}

	/**using order number $ordNum creeate 
	* Crete new order for user $email and StoreView $name using product $sku. Then creeate invoice using order number $ordNum.
	*@param $email
	*@param $name
	*@param $sku
	*@param $ordNum
	*/
	public function doCreateOrder($email, $name, $sku) {
		Core::debug("Creation Order started");
		// Open Order Page
		$this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
		// Create new Order
		$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/manageOrdersPage/buttons/createOrder"));
		// Fill fields Email and StoreView for searching user
		$this->type($this->getUiElement("admin/pages/sales/orders/createOrderPage/salectUserPageForOrder/inputs/searchByUserEmail"),$email);
		$this->type($this->getUiElement("admin/pages/sales/orders/createOrderPage/salectUserPageForOrder/inputs/searchByStoreName"),$name);
		// Searching and selecting user
		$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/salectUserPageForOrder/buttons/userSearch"));
		$this->pleaseWait();
		// check whether user exists
		$userText = $this->getText($this->getUiElement("admin/pages/sales/orders/createOrderPage/salectUserPageForOrder/links/selectUser"));
		if ($userText != 'No records found.') {
			$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/salectUserPageForOrder/links/selectUser"));
			$this->pleaseWait();
		} else {
			$this->setVerificationErrors("User does not exist");
			return false;
		}
		//selecting Store View
		if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/createOrderPage/selectSoreViewPageForOrder/PageElements/loadStoreViewPage"),10)) {
			$this->setVerificationErrors("creationOrder check 1: no loaded Store View page");
			return false;
		}
		$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/selectSoreViewPageForOrder/links/selecStoreView",$name));
		$this->pleaseWait();
		if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/createOrderPage/PageElements/orderPage"),10)) {
			$this->setVerificationErrors("creationOrder check 2: no loaded Order Creation page");
		}
		// Search and add product
		$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/buttons/addProduct"));
		if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/createOrderPage/PageElements/productSearchGrid"),10)) {
			$this->setVerificationErrors("creationOrder check 3: no loaded Product Search Grid");
		}
		$this->type($this->getUiElement("admin/pages/sales/orders/createOrderPage/input/searchByProductSKU"),$sku);
		$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/buttons/productSearch"));
		$this->pleaseWait();
		$productText = $this->getText($this->getUiElement("admin/pages/sales/orders/createOrderPage/links/selectProduct"));
		if ($productText != 'No records found.') {
			$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/links/selectProduct"));
			$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/buttons/addProductConfirm"));
			$this->pleaseWait();
		} else {
			$this->setVerificationErrors("Product does not exist");
			return false;
		}
		// Select Payment Method (Check / Money order)
		if ($this->isElementPresent($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/inputs/check"),10)){
			$this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/inputs/check"));
			$this->pleaseWait();
		} else {
			$this->setVerificationErrors("This Payment method is currently unavailable.");
			return false;
		}
		// Select Shhiping Method (Flat Rate)
		$this->click($this->getUiElement("admin/pages/sales/orders/createOrderPage/links/getShipping"));
		$this->pleaseWait();
		if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/createOrderPage/messages/verifyShipping"),10)) {
			$this->setVerificationErrors("Shipping Address is empty(or no Shipping Methods are available)");
			return false;
		} else {
		if (!$this->isElementPresent($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/flatrate"),10)) {
			$this->setVerificationErrors("This shipping method is currently unavailable.");
			return false;
		} else {
			$this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/flatrate"));
			$this->pleaseWait();
		}
		}
		// Sumbit Order
		$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/createOrderPage/buttons/sumbitOrder"));
		if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/createOrderPage/messages/verifyReqField"),10)) {
			$this->setVerificationErrors("Required field(s) empty");
			return false;
		} else {
		if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/createOrderPage/messages/orderNotCreated"),10)) {
			$etext = $this->getText($this->getUiElement("admin/pages/sales/orders/createOrderPage/messages/orderNotCreated"));
			$this->setVerificationErrors("Check 1: " . $etext);
		} else {
		if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/createOrderPage/messages/orderCreated"),10)) {
			$this->setVerificationErrors("creationOrder check 4: no success message about order creation");
		}
		}
		Core::debug("Creation Order finished");
		}
	}

	public function doOpenOrder($ordNum) {
		Core::debug("Open Order page started");
		// Open Order Page and Order
		$this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
		$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/manageOrdersPage/links/selectOdrer", $ordNum));
		Core::debug("Opening Order page finished");
	}

	public function doCreateInvoice() {
		Core::debug("Invoice creation started");
		if (!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/createInvoce"),10)){
			$this->setVerificationErrors("You cannot create an Invoice for this order");
		} else {
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/createInvoce"));
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/createInvoicePage/buttons/sumbitInvoice"));
		}
		if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/editOrderPage/messages/invoiceCreated"),10)) {
			$this->setVerificationErrors("creationInvoice check 5: no success message about Invoice creation");
		}
		Core::debug("Invoice creation finished");
	}

	public function doCreateShippment() {
		Core::debug("Shippment creation started");
		if (!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/createShip"),10)){
			$this->setVerificationErrors("You cannot create a Shippment for this order");
		} else {
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/createShip"));
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/createShippmentPage/buttons/sumbitShip"));
		}
		if (!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/editOrderPage/messages/ShipCreated"),10)) {
			$this->setVerificationErrors("creationShippment check 6: no success message about Shippment creation");
		}
		Core::debug("Shippment creation finished");
	}

	public function doCreateCreditMemo() {
		Core::debug("Credit Memo creation started");
		if (!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/createMemo"),10)){
			$this->setVerificationErrors("You cannot create a Credit Memo for this order");
		} else {
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/createMemo"));
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/createCreditMemoPage/buttons/sumbitMemo"));
		}
		if (!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/editOrderPage/messages/cMemoCreated"),10)) {
			$this->setVerificationErrors("creationCreditMemo check 7: no success message about Credit Memo creation");
		}
		Core::debug("Credit Memo creation finished");
	}

	public function doReOrder() {
		Core::debug("reOrder started");
		if (!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/reOrder"),10)){
			$this->setVerificationErrors("You cannot reOrder this order");
		} else {
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/editOrderPage/buttons/reOrder"));
			$this->clickAndWait($this->getUiElement("admin/pages/sales/orders/createOrderPage/buttons/sumbitOrder"));
		}
		if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/createOrderPage/messages/orderCreated"),10)) {
			$this->setVerificationErrors("creationCreditMemo check 8: no success message about ReOrder");
		}
		Core::debug("ReoRorder finished");
	}

	public function orderStatus($ordNum, $orderStatus) {
		$status = $this->getText($this->getUiElement("admin/pages/sales/orders/manageOrdersPage/pageElements/orderStatus",$ordNum));
		if ($status != $orderStatus) {
			$this->setVerificationErrors("Order has an incorrect status after creation order.The order status is $status, but must have the status $orderStatus");
		}
	}
}
