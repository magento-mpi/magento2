<?php

class Admin_Scope_SetUp extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/system/config/web');
        $this->setUiNamespace();
    }

    /**
     * Test storeView creation
     */
    function testSetUp()
    {

        // Get test parameters
        $params = array (
            'siteName' => Core::getEnvConfig('backend/scope/site/name'),
            'siteCode' => Core::getEnvConfig('backend/scope/site/code'),
            'storeName' => Core::getEnvConfig('backend/scope/store/name'),
            'storeViewName' => Core::getEnvConfig('backend/scope/store_view/name')
        );


        if ($this->model->doLogin()) {
            $this->model->configURL($params);
            $this->model->doReindex();
        }
    }
}
