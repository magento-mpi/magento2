<?php

class Admin_Category_Add extends Test_Admin_Category_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        // Get test parameters
        $this->_subCategoryName = Core::getEnvConfig('backend/managecategories/subcategoryname');
        $this->_parentSubCategoryName = Core::getEnvConfig('backend/managecategories/parentsubcategoryname');
        $this->_storeViewName = Core::getEnvConfig('backend/managestores/storeview/name');
    }


    /**
     * Test addion new sub category to the $StoreView store view
     *
     */

    function testSubCategoryCreation() {
        Core::debug("testSubCategoryCreation started");
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addSubCategory(  $this->_subCategoryName, $this->_parentSubCategoryName, $this->_storeViewName );
        Core::debug("testSubCategoryCreation finished");
    }
}