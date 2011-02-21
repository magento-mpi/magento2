<?php

class Admin_Product_RequiredFields_AddConfigurableProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Configurable Product by filling required fields
     */
    function testConfigurableProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Configurable Product',
            'attribute_set'         => 'smoke_attrSet',
            'attrib_for_conf_prod'  => 'Dropdown',
            'name'                  => 'Configurable Product 01.Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'CP-01',
            'status'                => 'Enabled',
            'price'                 => rand(100, 300),
            'tax_class'             => 'None',
            'manage_stock'          => 'Yes',
            'stock_availability'    => 'In Stock',
            'website_name'          => 'SmokeTestSite',
            'category_name'         => 'st-subcat'
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}