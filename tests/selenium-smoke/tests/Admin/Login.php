<?php

class Admin_Login extends Test_Admin_Abstract
{

    /**
     * check test login to system
     *
     */

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();
    }

    function testUserCreation() {
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
    }
}