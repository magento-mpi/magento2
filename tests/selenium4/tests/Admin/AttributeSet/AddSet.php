<?php

class Admin_AttributeSet_AddSet extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/attributeset');
        $this->setUiNamespace();
    }
    
    /**
    * Test addition new Attribute Set
    */
    function testAttributeSetCreation()
    {
        if ($this->model->doLogin()) {
            $this->model->doDeleteAtrSet();
            $this->model->doCreateAtrSet();
        }
    }
}