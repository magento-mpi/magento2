<?php

class Admin_ManageStores_Sitecreation extends Test_Admin_ManageStores_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();

        //Get TestData
        $this->_siteName = Core::getEnvConfig('backend/managestores/site/name');
        $this->_siteCode = Core::getEnvConfig('backend/managestores/site/code');
        $this->_siteOrder = Core::getEnvConfig('backend/managestores/site/sortorder');
    }

    /**
     *  Site Creation Test
     */
    function testSiteCreation()
    {
        //Test Flow
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->adminSiteCreation($this->_siteName, $this->_siteCode, $this->_siteOrder);
        }
    }
}
