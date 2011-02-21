<?php

class Admin_Product_DeleteProduct extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/product');
        $this->setUiNamespace();
    }

    /**
     * Test Case: Delete Product
     */
    function testProductDeletion()
    {
        $arrayData = array(
            'search_product_name' => 'Configurable Product 01.Required Fields',
            'search_product_sku' => 'CP-01'
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteProduct($arrayData);
        }
    }

}