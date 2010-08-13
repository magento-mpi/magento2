<?php

class Admin_Category_RootAdd extends Test_Admin_Category_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    /**
     * Test addion new root category to the $StoreView store view
     *
     */

    function testRootCategoryCreation() {
        $this->debug("testRootCategoryCreation started");
        //Test Data
        $rootCategoryName = "SLRootTestCategory";
        $storeViewName = "All Store Views";
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addRootCategory($rootCategoryName, $storeViewName );
        $this->debug("testRootCategoryCreation finished");
    }
}