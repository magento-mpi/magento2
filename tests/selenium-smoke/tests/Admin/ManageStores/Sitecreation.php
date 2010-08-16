<?php

class Admin_ManageStores_Sitecreation extends Test_Admin_ManageStores_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    function testSiteCreation() {
        $this->debug("testSiteCreation started");
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->adminSiteCreation($this->_siteName, $this->_siteCode, $this->_siteOrder);
        $this->debug("testSiteCreation finished");
        sleep(10);
    }
}
