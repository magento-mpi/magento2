<?php

class Admin_Scope_StoreView extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/scope/storeview');
        $this->setUiNamespace();
    }

    /**
     * Test storeView creation
     */
    function testStoreViewCreation() {
        $this->model->doLogin();
        $this->model->doCreate();
    }
}
