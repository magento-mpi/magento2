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


      // check for error message
      if ($this->waitForElement($this->getUiElement("admin/messages/error"),1)) {
        $etext = $this->getText($this->getUiElement("admin/messages/error"));
        $this->setVerificationErrors("Check 1: " . $etext);
      } else {
          // Check for success message
        if (!$this->waitForElement($this->getUiElement("admin/messages/success"),60)) {
            $this->setVerificationErrors("Check 2: no success message");
        }
        //Check for some specific validation errors:
        // sku must be unique
        if ($this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/messages/skumustbeunique"),2)) {
            $this->setVerificationErrors("Check 3: SKU must be unique");
        }
      }
      sleep(20);
      Core::debug("addSimpleProduct finished");
    }


   /**
     * Open product for the editing
     * @param $sku - contains reurn
     * @returns true on success
     * @returns false and setVerificationErrors() on
     */
    public function doOpenProduct($sku = null)
    {
        $this->click($this->getUiElement("admin/topmenu/catalog/manageproducts"));
        $this->waitForPageToLoad("90000");
        $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/buttons/resetFilter"));
        $this->pleaseWait();
        $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/inputs/filter_sku"),$sku);
        $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/buttons/search"));
        $this->pleaseWait();

        if ($this->waitForElement($this->getUiElement('admin/pages/catalog/categories/manageproducts/elements/filteredProduct',$sku),30)) {
            $this->click($this->getUiElement('admin/pages/catalog/categories/manageproducts/elements/filteredProduct',$sku));
            $this->waitForPageToLoad("90000");
            return true;
        } else {
            $this->setVerificationErrors("Product with sku=" . $sku . " could not be loaded");
            return false;
        }
    }

    /**
     * Duplicate product with specified @sku
     * @param $sku - original product Sku
     * @param $duplicatedSku - duplicated product Sku
     *
     */
    public function duplicateProduct($sku, $duplicatedSku)
    {
        $result = true;
      //Open source product
      if ($this->doOpenProduct($sku)) {
          //if such product exists ...
          //Duplicate
          $this->clickAndWait($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/buttons/duplicate"));
          // Check for success message
          if (!$this->waitForElement($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/messages/productDuplicated"),20)) {
              $this->setVerificationErrors("duplicate check 1: no success duplicated message");
              $result = false;
          }
          //Change SKU
          $this->type ($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/inputs/sku"),$duplicatedSku);
          //Save product
          $this->click($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/buttons/save"));
          // Check for success message
          if (!$this->waitForElement($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/messages/productsaved"),20)) {
              $this->setVerificationErrors("duplicate check 2: no success saved message");
              $result = false;
          }
          //Check for some specific validation errors:
          // sku must be unique
          if ($this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/manageproducts/product/messages/skumustbeunique"),2)) {
            $this->setVerificationErrors("duplicate check 3: SKU must be unique error");
            $result = false;
          }
      } else {
        //product with $sku does not exists
           $result = false;
      }
      return $result;

    }
}

