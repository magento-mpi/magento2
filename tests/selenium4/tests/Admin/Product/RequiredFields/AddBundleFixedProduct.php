<?php

class Admin_Product_RequiredFields_AddBundleFixedProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Bundle.Fixed Product by filling required fields
     */
    function testBundleFixedProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Bundle Product',
            'attribute_set'         => 'smoke_attrSet',
            'name'                  => 'Bundle Product 02(Fixed).Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku_type'              => 'Fixed',
            'sku'                   => 'BP-02',
            'weight_type'           => 'Fixed',
            'weight'                => rand(10, 100),
            'status'                => 'Enabled',
            'price_type'            => 'Fixed',
            'price'                 => rand(100, 300),
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