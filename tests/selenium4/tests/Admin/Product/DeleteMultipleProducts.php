<?php

class Admin_Product_DeleteMultipleProducts extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/product/simple');
        $this->setUiNamespace();
    }

    /**
    * Test addition new Attribute Set
    */
    function testTwoSimpleProductDeletion()
    {
        if ($this->model->doLogin()) {
            $this->model->doDeleteMultipleProducts();
        }
    }
}