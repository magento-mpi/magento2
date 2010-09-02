<?php

class Admin_ManageStores_Storecreation extends Test_Admin_ManageStores_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        //Get TestData
        $this->_siteName = Core::getEnvConfig('backend/managestores/site/name');
        $this->_storeName = Core::getEnvConfig('backend/managestores/store/storename');
        $this->_rootCategoryName = Core::getEnvConfig('backend/managecategories/rootname');
    }

    /**
     *  Store Creation Test
     */
    function testStoreCreation() {
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->adminStoreCreation($this->_siteName, $this->_storeName, $this->_rootCategoryName);
        };
    }
}

