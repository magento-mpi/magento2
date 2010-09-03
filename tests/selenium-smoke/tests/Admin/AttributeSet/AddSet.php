<?php

class Admin_AttributeSet_AddSet extends Test_Admin_AttributeSet_Abstract {

    /**
    * Setup procedure.
    * Must be overriden in the children having any additional code prepended with parent::setUp();
    */
    function setUp() {
        parent::setUp();

        // Get test parameters
        $this->_attributeSetName = Core::getEnvConfig('backend/attributeSet/setName');
    }

    /**
    * Test addition new Attribute Set
    */
    function testAttributeSetCreation() {
        // Test Flow
        if ($this->adminLogin($this->_baseUrl, $this->_userName, $this->_password)) {
            $this->addSet($this->_attributeSetName);
        }
    }
}