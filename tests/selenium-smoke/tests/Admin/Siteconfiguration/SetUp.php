<?php

class Admin_Siteconfiguration_SetUp extends Test_Admin_Siteconfiguration_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    function testSiteConfiguration() {
        Core::debug("testSiteCreation started");
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->configURL();
        $this->reindex();
        Core::debug("testSiteCreation finished");
    }
}
