<?php

class Admin_ManageStores_Storecreation extends Test_Admin_ManageStores_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    function testStoreCreation() {
        Core::debug("testStoreCreation started");
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);       
        $this->adminStoreCreation($this->_siteName, $this->_storeName, $this->_rootCategoryName);
        Core::debug("testSiteCreation finished");
    }
}

