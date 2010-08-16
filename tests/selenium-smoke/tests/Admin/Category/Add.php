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
        $this->_storeview = Core::getEnvConfig('backend/managecategories/storeview');
    }

    /**
     * Test addion new sub category to the $StoreView store view
     *
     */

    function testSubCategoryCreation() {
        $this->debug("testSubCategoryCreation started");
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addSubCategory(  $this->_subCategoryName, $this->_parentSubCategoryName, $this->_storeview );
        $this->debug("testSubCategoryCreation finished");
    }
}