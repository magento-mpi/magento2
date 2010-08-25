<?php

class Admin_Scope_Site extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/scope/site');
        $this->setUiNamespace();
    }

    /**
     * Test website creation
     */
    function testSiteCreation() {
        $this->model->doLogin();
        $this->model->doCreate();
    }
}
