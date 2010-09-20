<?php

class Admin_User_Add extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/user');
        $this->setUiNamespace();
    }

    /**
     * Test website creation
     */
    function testUserCreate() {
        if ($this->model->doLogin()) {
            $this->model->doDelete();
            $this->model->doCreate();
        }
    }
}
