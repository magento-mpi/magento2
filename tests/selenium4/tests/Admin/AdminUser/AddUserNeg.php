<?php

class Admin_AdminUser_AddUserNeg extends TestCaseAbstract {

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
     * Negative Test Admin User Create
     */
    function testAdminUserCreateNeg()
    {
        $userData = array(
            'user_name'         => '',
            'user_first_name'   => '',
            'user_last_name'    => '',
            'user_email'        => '',
            'user_password'     => '',
            'user_confirmation' => ''
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAdminUser($userData);
        }
    }

}
