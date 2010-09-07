<?php

class Admin_AttributeSet_AddSet extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/AttributeSet/AddSet');
        $this->setUiNamespace();
    }
    
    /**
    * Test addition new Attribute Set
    */
    function testAttributeSetCreation()
    {
        $this->model->doLogin();
        $this->model->doCreateAtrSet();
        $this->model->doOpenAtrSet();
        $this->model->doDeleteAtrSet();
    }
}