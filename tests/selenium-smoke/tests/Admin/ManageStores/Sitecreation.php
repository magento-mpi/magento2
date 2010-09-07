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
    }

    /**
     *  Site Creation Test
     */
    function testSiteCreation()
    {
        //Test Data
        $siteName = Core::getEnvConfig('backend/managestores/site/name');
        $siteCode = Core::getEnvConfig('backend/managestores/site/code');
        $siteOrder = Core::getEnvConfig('backend/managestores/site/sortorder');
        //Test Flow
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
              $this->doSiteOpen($siteName);
//            $this->doSiteCreate($siteName, $siteCode, $siteOrder);
        }
    }
}
