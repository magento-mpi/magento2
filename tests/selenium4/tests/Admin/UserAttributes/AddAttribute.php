<?php

class Admin_UserAttributes_AddAttribute extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/attributes');
        $this->setUiNamespace();
    }

    /**
     * Test addition new Attribute
     */
    function testTextFieldAttributeCreation() {
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute();
        }
    }

}