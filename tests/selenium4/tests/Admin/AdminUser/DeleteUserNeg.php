<?php

class Admin_AdminUser_DeleteUserNeg extends TestCaseAbstract {

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
     * Negative Test Admin User delete
     */
    function testAdminUserDeleteNeg()
    {
        $userData = array(
            'search_admin_user_name' => 'admin',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteAdminUser($userData);
        }
    }

}
