<?php

class Admin_Product_DublicateSimple extends TestCaseAbstract {

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
    function testSimpleProductDublication() {
        if ($this->model->doLogin()) {
            $this->model->duplicateProduct();
        }
    }

}