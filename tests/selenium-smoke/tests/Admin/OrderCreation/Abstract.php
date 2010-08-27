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


    /**
     * Crete new order for user $email and StoreView $name using product $sku
     *@param $email
     *@param $name
     *@param $sku
     */
    public function adminOrderCreate($email, $name, $sku)
    {
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
 //всплывающие окно
      $this->pleaseWait();
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/selectUser"));
  //всплывающие окно
      $this->pleaseWait();
      //selecting Store View
      if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/loadStoreViewPage"),10)) {
          $this->setVerificationErrors("creationOrder check 1: no load Store View page"); }
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/selecStoreView",$name));
 //всплывающие окно
      $this->pleaseWait();
      if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/orderPage"),10)) {
          $this->setVerificationErrors("creationOrder check 2: no load Order Creation page"); }
      // Search and add product
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/addProduct"));
      if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/productSearchGrid"),10)) {
          $this->setVerificationErrors("creationOrder check 3: no load Product Search Grid"); }
      $this->type($this->getUiElement("admin/pages/sales/orders/creationOrder/search/bySKU"),$sku);
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/productSearch"));
 //всплывающие окно
      $this->pleaseWait();
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/selectProduct"));
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/buttons/addProductConfirm"));
 //всплывающие окно
      $this->pleaseWait();
      // Select Payment Method (Check / Money order)
      $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/inputs/check"));
 //всплывающие окно\
      $this->pleaseWait();
      // Select Shhiping Method (Flat Rate)
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/getShipping"));
 //всплывающие окно
      $this->pleaseWait();
      $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/flatrate"));
 // всплывающие окно
      $this->pleaseWait();
      // Sumbit Order
      $this->click($this->getUiElement("admin/pages/sales/orders/creationOrder/messages/orderCreated"));
      if (!$this->waitForElement($this->getUiElement("admin/pages/sales/orders/creationOrder/loading/productSearchGrid"),10)) {
          $this->setVerificationErrors("creationOrder check 4: no success message"); }
    }
}