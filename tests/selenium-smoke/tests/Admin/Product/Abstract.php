<?php
/**
 * Abstract test class for Admin/Category module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Product_Abstract extends Test_Admin_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();
   }


    /**
     * Adds new simple product with next patrameters:
     *@param sku
     *@param productName
     *@param categoryName
     *@param webSiteName
     *@param storeViewName
     *
     *
     */
    public function addSimpleProduct($paramArray) {
      Core::debug("addSimpleProduct started");
      // Open Manage Products Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/catalog/manageproducts"));
      $this->clickAndWait ($this->getUiElement("admin/pages/catalog/categories/manageproducts/buttons/addproduct"));
      $this->clickAndWait ($this->getUiElement("admin/pages/catalog/categories/manageproducts/buttons/addproductcontinue"));
      //Fill Product Page
      //General Tab
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/name"),$paramArray["productName"]);
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/description"),$paramArray["description"]);
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/short_description"),$paramArray["short_description"]);
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/sku"),$paramArray["sku"]);
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/weight"),$paramArray["weight"]);
      $this->select($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/selectors/status"),"label=Enabled");
      // Price Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/price"));
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/price"),$paramArray["price"]);
      $this->select($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/selectors/tax_class"),"label=None");
      // Inventory Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/inventory"));
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/inventory_qty"),$paramArray["quantity"]);
      $this->select($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/selectors/inventory_stock_availability"),"label=In Stock");
      // Websites Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/websites"));
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/website",$paramArray["webSiteName"]));
      // Categories Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/categories"));
      $this->pleaseWait();
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/category",$paramArray["categoryName"]));
      //Save product
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/buttons/save"));

      
      // Check for success message
      if (!$this->waitForElement($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/messages/productsaved"),10)) {
          $this->setVerificationErrors("addSimpleProduct check 1: no success message");

          //Check for some specific validation errors:
          // sku must be unique
          if ($this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/messages/skumustbeunique"),2)) {
            $this->setVerificationErrors("addSimpleProduct check 2: SKU must be unique");
          }
      }
      Core::debug("addSimpleProduct finished");
    }


}

