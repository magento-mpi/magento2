<?php

class Admin_Product_AddProduct extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/product');
        $this->setUiNamespace();
    }

    /**
     * Test addition new Product
     */
    function testProductCreation() {
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct();
        }
    }

}