<?php

class Admin_User_Add extends Test_Admin_User_Abstract
{

    /**
     * Add new user to the system
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
        $this->debug("testUserCreation started");
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addUser("test");
        $this->debug("testUserCreation finished");
        sleep(10);
    }
}