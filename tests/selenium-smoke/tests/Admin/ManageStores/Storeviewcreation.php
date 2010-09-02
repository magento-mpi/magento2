<?php

class Admin_ManageStores_Storeviewcreation extends Test_Admin_ManageStores_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        //Get TestData
        $this->_storeName = Core::getEnvConfig('backend/managestores/store/storename');
        $this->_storeviewName = Core::getEnvConfig('backend/managestores/storeview/name');
        $this->_storeviewCode = Core::getEnvConfig('backend/managestores/storeview/code');
        $this->_storeviewStatus = Core::getEnvConfig('backend/managestores/storeview/status');
    }

    /**
     *  StoreView Creation Test
     */
    function testStoreViewCreation()
    {
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->adminStoreViewCreation($this->_storeName, $this->_storeviewName, $this->_storeviewCode, $this->_storeviewStatus);
        }
    }
}

