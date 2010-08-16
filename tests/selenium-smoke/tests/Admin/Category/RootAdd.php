<?php
/**
 * Abstract test class for Admin/Category/Add module
 *
 * @author Magento Inc.
 */

class Admin_Category_RootAdd extends Test_Admin_Category_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

       // Get test parameters
       $this->_rootCategoryName  = Core::getEnvConfig('backend/managecategories/rootname');
       $this->_storeView = Core::getEnvConfig('backend/managestores/storeview/name');
    }

    /**
     * Test addion new root category to the $StoreView store view
     *
     */

    function testRootCategoryCreation() {
        Core::debug("testRootCategoryCreation started");
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addRootCategory( $this->_rootCategoryName,  $this->_storeView );
        Core::debug("testRootCategoryCreation finished");
    }
}