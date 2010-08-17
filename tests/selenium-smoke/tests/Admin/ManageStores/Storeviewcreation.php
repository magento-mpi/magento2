<?php

class Admin_ManageStores_Storeviewcreation extends Test_Admin_ManageStores_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    function testStoreViewCreation() {
        Core::debug("testSiteCreation started");
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->adminStoreViewCreation($this->_storeName, $this->_storeviewName, $this->_storeviewCode, $this->_storeviewStatus);
        Core::debug("testSiteCreation finished");
    }
}

