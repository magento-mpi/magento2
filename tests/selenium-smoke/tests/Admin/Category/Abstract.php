<?php
/**
 * Abstract test class for Admin/Category module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Category_Abstract extends Test_Admin_Abstract
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
     * Adds new root category into the
     *@param $CategorName
     *@param $StoreViewName
     * 
     */

    public function addRootCategory($CategorName, $StoreViewName) {
      $this->debug("addRootCategory started");
      // Open Manage Categories Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/catalog/categories/managecategories"));
      // Add new root ca
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/addrootcategory"));
      $this->pleaseWait();
      // Fill all fields
      $this->type($this->getUiElement("admin/pages/catalog/categories/managecategories/inputs/name"),$CategorName);
      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/isactive"),"label=Yes");
      // Save category
      $this->clickAndWait($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/savecategory"));
      $this->pleaseWait();
      // Check for success message
      if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"))) {
        $this->setVerificationErrors("addRootCategory check 1: no success message");
      } 
      $this->debug("addRootCategory finished");
    }

}

