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
        // Test Data

        // Test Flow
        if ($this->model->doLogin()) {
            $this->model->doDeleteProduct(array ('sku' => $this->model->productData['duplicatedSku']));
            $this->model->duplicateProduct();
        }
    }
}