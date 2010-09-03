<?php

class Admin_User_Add extends Test_Admin_User_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    /**
     * Add new user from admin to the system
     *
     */
    function testUserCreation() {
        //Test Data
        $userName = Core::getEnvConfig('backend/user/name');
        //Test Flow
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->doDeleteUser($userName);
            $this->addUser($userName);
        }
    }
}