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
    public function adminOrderCreate($email, $name, $sku) {
        Core::debug("Creation Order started");
        // Open Order Page
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        // Create new Order
        $this->clickAndWait($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/createOrder"));
        // Fill fields Email and StoreView for searching user
        $this->type($this->getUiElement("admin/pages/sales/orders/creationOrder/search/byEmail"),$email);
        $this->type($this->getUiElement("admin/pages/sales/orders/creationOrder/search/byStoreName"),$name);
        // Searching and selecting user
        $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/userSearch"));
        $this->pleaseWait();
        // check whether user exists
        $userText = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/selectUser"));
        if ($userText != 'No records found.') {
            $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/selectUser"));
            $this->pleaseWait();
        } else {
            $this->setVerificationErrors("User does not exist");
            return false;
        }
        //selecting Store View
        if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/loadStoreViewPage"),10)) {
            $this->setVerificationErrors("creationOrder check 1: no load Store View page");
        }
        $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/selecStoreView",$name));
        $this->pleaseWait();
        if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/orderPage"),10)) {
            $this->setVerificationErrors("creationOrder check 2: no load Order Creation page");
        }
        // Search and add product
        $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/addProduct"));
        if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/productSearchGrid"),10)) {
            $this->setVerificationErrors("creationOrder check 3: no load Product Search Grid");
        }
        $this->type($this->getUiElement("admin/pages/sales/orders/creationOrder/search/byProductSKU"),$sku);
        $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/productSearch"));
        $this->pleaseWait();
        $productText = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/selectProduct"));
        if ($productText != 'No records found.') {
            $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/selectProduct"));
            $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/addProductConfirm"));
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
        $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/getShipping"));
        $this->pleaseWait();
        if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/creationOrder/verifyShipping"),10)) {
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
        $this->clickAndWait($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/sumbitOrder"));
        if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/creationOrder/verifyReqField"),10)) {
            $this->setVerificationErrors("Required field(s) empty");
            return false;
        } else {
        if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/creationOrder/orderNotCreated"),10)) {
            $etext = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/orderNotCreated"));
            Core::debug($etext );
            $this->setVerificationErrors("Check 1: " . $etext);
        } else {
        if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/messages/orderCreated"),10)) {
            $this->setVerificationErrors("creationOrder check 4: no success message about order creation");
        }
        }
        Core::debug("Creation Order finished");
        }
    }
    
    public function openOrder($ordNum) {
        Core::debug("Open Order page started");
        // Open Order Page and Order
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $this->clickAndWait($this->getUiElement("admin/pages/sales/orders/creationInvoice/selectOdrer", $ordNum));
        Core::debug("Opening Order page finished");
    }

    public function createInvoice() {
        Core::debug("Invoice creation started");
        $this->clickAndWait($this->getUiElement("admin/pages/sales/orders/creationInvoice/buttons/createInvoce"));
        $this->clickAndWait($this->getUiElement("admin/pages/sales/orders/creationInvoice/buttons/sumbitInvoice"));
        if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationInvoice/messages/invoiceCreated"),10)) {
            $this->setVerificationErrors("creationInvoice check 5: no success message about Invoice creation");
        }
        Core::debug("Invoice creation finished");
    }
}
