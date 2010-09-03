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
    }


    /**
     * Test addion new subcategory to the parentCategory
     *
     */
    function testSubCategoryCreation() {
        // Test Flow
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->addSubCategory($this->_subCategoryName, $this->_parentSubCategoryName);
        }
    }
}