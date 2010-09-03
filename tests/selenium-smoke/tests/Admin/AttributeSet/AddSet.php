<?php

class Admin_AttributeSet_AddSet extends Test_Admin_AttributeSet_Abstract {

    /**
    * Setup procedure.
    * Must be overriden in the children having any additional code prepended with parent::setUp();
    */
    function setUp() {
        parent::setUp();

        // Get test parameters
        $this->_setName = Core::getEnvConfig('backend/attributeSet/setName');
    }

    /**
    * Test addition new Attribute Set
    */
    function testAttributeSetCreation() {
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->doCreateAtrSet($this->_setName);
        $this->doOpenAtrSet($this->_setName);
        $this->doDeleteAtrSet();
    }
}