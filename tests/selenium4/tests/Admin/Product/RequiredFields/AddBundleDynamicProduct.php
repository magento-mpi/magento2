<?php

class Admin_Product_RequiredFields_AddBundleDynamicProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Bundle.Dynamic Product by filling required fields
     */
    function testProductDeletion()
    {
        $arrayData = array(
            'type'                  => 'Bundle Product',
            'attribute_set'         => 'smoke_attrSet',
            'name'                  => 'Bundle Product 01(Dynamic).Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku_type'              => 'Dynamic',
            'sku'                   => 'BP-01',
            'weight_type'           => 'Dynamic',
            'weight'                => rand(10, 100),
            'status'                => 'Enabled',
            'price_type'            => 'Dynamic',
            'tax_class'             => 'None',
            'manage_stock'          => 'Yes',
            'stock_availability'    => 'In Stock',
            'website_name'          => 'SmokeTestSite',
            'category_name'         => 'st-subcat',
            'bundle_options_title'  => 'Bundle Options Title 01',
            'search_product_sku'      => array('SP-01', 'VP-01', 'DP-01')
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}