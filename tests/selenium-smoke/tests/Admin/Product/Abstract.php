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
    public function addSimpleProduct($sku, $productName, $categoryName, $webSiteName, $storeViewName, $price) {
      Core::debug("addSimpleProduct started");
      // Open Manage Products Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/catalog/manageproducts"));
      $this->clickAndWait ($this->getUiElement("admin/pages/catalog/categories/manageproducts/buttons/addproduct"));
      $this->clickAndWait ($this->getUiElement("admin/pages/catalog/categories/manageproducts/buttons/addproductcontinue"));
      //Fill Product Page
      //General Tab
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/name"),$productName);
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/description"),$productName." description");
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/short_description"),$productName." short_description");
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/name"),$productName);
      // Price Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/price"));
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/price"),$price);
      $this->select($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/selectors/tax_class"),"label=None");
      // Inventory Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/inventory"));
      $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/inventory_qty"),"1000");
      $this->select($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/selectors/inventory_stock_availability"),"label=In Stock");
      // Websites Tab
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/websites"));
      $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/tabs/websites"));

      // Check for success message
      if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"))) {
        $this->setVerificationErrors("addRootCategory check 1: no success message");
      }
      sleep(20);
      Core::debug("addSimpleProduct finished");
    }


}

