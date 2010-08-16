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
        $this->debug("testStoreCreation started");
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->adminSiteCreation($this->_siteName, $this->_siteCode, $this->_siteOrder);
        $this->adminStoreCreation($this->_siteName, $this->_storeName, $this->_rootCategory);
        $this->debug("testSiteCreation finished");
        sleep(10);
    }
}

