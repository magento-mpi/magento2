<?php

class Admin_AdminUser_AddUser extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/adminuser');
        $this->setUiNamespace();
    }

    /**
     * Test Admin User Create
     */
    function testAdminUserCreate()
    {
        $userData = Core::getEnvConfig('backend/admin_user');
        if ($this->model->doLogin()) {
            $this->model->doCreateAdminUser($userData);
        }
    }

}
