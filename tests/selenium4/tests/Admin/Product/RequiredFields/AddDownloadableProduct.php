<?php

class Admin_Product_RequiredFields_AddDownloadableProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Downloadable Product by filling required fields
     */
    function testDownloadableProductCreation()
    {
        $arrayData = array(
            'type'                              => 'Downloadable Product',
            'attribute_set'                     => 'smoke_attrSet',
            'name'                              => 'Downloadable Product 01.Required Fields',
            'description'                       => 'Product description',
            'short_description'                 => 'Product short description',
            'sku'                               => 'DP-01',
            'status'                            => 'Enabled',
            'price'                             => rand(100, 300),
            'tax_class'                         => 'None',
            'manage_stock'                      => 'Yes',
            'inventory_qty'                     => '100',
            'stock_availability'                => 'In Stock',
            'website_name'                      => 'SmokeTestSite',
            'category_name'                     => 'st-subcat',
            'downloadable_links_purchase_type'  => 'No',
            'downloadable_sample_title'         => 'sample_title_1',
            'downloadable_sample_url'           => 'http://sample_url_1',
            'downloadable_link_title'           => 'link_title_1',
            'downloadable_link_url'             => 'http://link_url_1'
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}