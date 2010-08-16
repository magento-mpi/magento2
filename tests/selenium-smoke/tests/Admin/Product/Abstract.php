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
     *@param $productName
     *@param $categoryName
     *@param $webSiteName
     *@param $storeViewName
     *
     */
    public function addSimpleProduct($productName, $categoryName, $webSiteName, $storeViewName) {
      Core::debug("addSimpleProduct started");
      // Open Manage Categories Page

      // Check for success message
      if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/categories/managecategories/messages/categorysaved"))) {
        $this->setVerificationErrors("addRootCategory check 1: no success message");
      }
      Core::debug("addSimpleProduct finished");
    }


}

