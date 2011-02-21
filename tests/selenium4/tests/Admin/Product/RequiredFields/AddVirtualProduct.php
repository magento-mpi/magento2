<?php

class Admin_Product_RequiredFields_AddVirtualProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Virtual Product by filling required fields
     */
    function testVirtualProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Virtual Product',
            'attribute_set'         => 'smoke_attrSet',
            'name'                  => 'Virtual Product 01.Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'VP-01',
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