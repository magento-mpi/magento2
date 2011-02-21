<?php

class Admin_Product_RequiredFields_AddSimpleProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Simple Product by filling required fields
     */
    function testSimpleProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Simple Product',
            'attribute_set'         => 'smoke_attrSet',
            'name'                  => 'Simple Product 01.Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'SP-01',
            'weight'                => rand(10, 100),
            'status'                => 'Enabled',
            'price'                 => rand(100, 300),
            'tax_class'             => 'None',
            'manage_stock'          => 'Yes',
            'inventory_qty'         => '100',
            'stock_availability'    => 'In Stock',
            'website_name'          => 'SmokeTestSite',
            'category_name'         => 'st-subcat'
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}