<?php

class Admin_Login extends Test_Admin_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    /**
     * Test user login to admin
     *
     */
    function testAdminLogin() {
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
    }
}