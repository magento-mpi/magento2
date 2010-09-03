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
     * Adds new sub $categoryName for $parentCategoryName 
     *@param $categorName
     *@param $parentCategoryName
     * 
     */
    public function addSubCategory($categoryName, $parentCategoryName)
    {
      Core::debug("addSubCategory started",7);

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
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/tabs/displaysettings"));
      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/isanchor"),"label=Yes");

      // Save category
      $this->clickAndWait($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/savecategory"));
      $this->pleaseWait();

      // check for error message
      if ($this->waitForElement($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/error"),1)) {
        $etext = $this->getText($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/error"));
        $this->setVerificationErrors("Check 1: " . $etext);
      } else {
      // Check for success message
          if (!$this->waitForElement($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"),1)) {
            $this->setVerificationErrors("Check 2: no success message");
          }
      }

      Core::debug("addSubCategory finished",7);
    }

    /**
     * Adds new root $categoryName category into the $storeViewName store view
     *@param $categoryName
     *@param $storeViewName
     *
     */
    public function addRootCategory($categoryName)
    {
      Core::debug("addRootCategory started",7);

      // Open Manage Categories Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/catalog/categories/managecategories"));

      // Add new root category
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/addrootcategory"));
      $this->pleaseWait();

      // Fill all fields
      $this->type($this->getUiElement("admin/pages/catalog/categories/managecategories/inputs/name"),$categoryName);
      $this->select($this->getUiElement("admin/pages/catalog/categories/managecategories/selectors/isactive"),"label=Yes");

      // Save category
      $this->click($this->getUiElement("admin/pages/catalog/categories/managecategories/buttons/savecategory"));
      $this->pleaseWait();

      // check for error message
      if ($this->waitForElement($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/error"),1)) {
        $etext = $this->getText($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/error"));
        $this->setVerificationErrors("Check 1: " . $etext);
      } else {
      // Check for success message
          if (!$this->waitForElement($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"),1)) {
            $this->setVerificationErrors("Check 2: no success message");
          }
      }

      Core::debug("addRootCategory finished",7);
    }
}

