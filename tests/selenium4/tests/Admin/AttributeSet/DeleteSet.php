<?php

class Admin_AttributeSet_DeleteSet extends TestCaseAbstract
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
    * Test deletion Attribute Set
    */
    function testAttributeSetDeletion()
    {
        if ($this->model->doLogin()) {
        if ($this->model->doOpenAtrSet()) {
            $this->model->doDeleteAtrSet();
        }
        }
    }
}