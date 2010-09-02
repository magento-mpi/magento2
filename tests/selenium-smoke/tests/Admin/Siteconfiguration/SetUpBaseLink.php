<?php

class Admin_Siteconfiguration_SetUpBaseLink extends Test_Admin_Siteconfiguration_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        // Get test parameters
        $this->_siteName = Core::getEnvConfig('backend/managestores/site/name');
        $this->_siteCode = Core::getEnvConfig('backend/managestores/site/code');
    }

    /**
     * Test configuring of website baseurl values
     *
     */
    function testSiteConfiguration() {
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->configURL();
            $this->doReindex();
        }
    }
}
