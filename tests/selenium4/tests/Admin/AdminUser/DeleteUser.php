<?php

class Admin_AdminUser_DeleteUser extends TestCaseAbstract {

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
     * Test Admin User Delete
     */
    function testAdminUserDelete()
    {
        $userData = array(
            'search_admin_user_name'        => 'testAdmin',
            'search_admin_user_firstname'   => 'FName',
            'search_admin_user_lastname'    => 'LName',
            'search_admin_user_email'       => 'test_admin@magento.com'
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteAdminUser($userData);
        }
    }

}
