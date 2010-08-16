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
     * Adds new sub $categoryName for $parentCategoryName into the $storeViewName store view
     *@param $categorName
     *@param $parentCategoryName
     *@param $storeViewName
     * 
     */
    public function addSubCategory($categoryName, $parentCategoryName, $storeViewName) {
      Core::debug("addSubCategory started");
      // Open Manage Categories Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/catalog/categories/managecategories"));
      //Select Parent Category
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/locators/parentcategory",$parentCategoryName));
      // Add new sub category
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/addsubcategory"));
      $this->pleaseWait();
      // Fill all fields
      $this->type($this->getUiElement("admin/pages/catalog/categories/managecategories/inputs/name"),$categoryName);
      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/isactive"),"label=Yes");
//      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/storeswitcher"),"label=".$storeViewName);
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/tabs/displaysettings"));
      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/isanchor"),"label=Yes");
      // Save category
      $this->clickAndWait($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/savecategory"));
      $this->pleaseWait();
      // Check for success message
      if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"))) {
        $this->setVerificationErrors("addRootCategory check 1: no success message");
      } 
      Core::debug("addSubCategory finished");
    }

    /**
     * Adds new root $categoryName category into the $storeViewName store view
     *@param $categoryName
     *@param $storeViewName
     *
     */
    public function addRootCategory($categoryName, $storeViewName) {
      Core::debug("addRootCategory started");
      // Open Manage Categories Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/catalog/categories/managecategories"));
      // Add new root ca
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/addrootcategory"));
      $this->pleaseWait();
      // Fill all fields
      $this->type($this->getUiElement("admin/pages/catalog/categories/managecategories/inputs/name"),$categoryName);
      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/isactive"),"label=Yes");
//      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/storeswitcher"),"label=".$storeViewName);
      // Save category
      $this->clickAndWait($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/savecategory"));
      $this->pleaseWait();
      // Check for success message
      if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"))) {
        $this->setVerificationErrors("addRootCategory check 1: no success message");
      }
      Core::debug("addRootCategory finished");
    }

}

