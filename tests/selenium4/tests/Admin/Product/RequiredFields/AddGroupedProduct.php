<?php

class Admin_Product_RequiredFields_AddGroupedProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Grouped Product by filling required fields
     */
    function testGroupedProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Grouped Product',
            'attribute_set'         => 'smoke_attrSet',
            'name'                  => 'Grouped Product 01.Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'GP-01',
            'status'                => 'Enabled',
            'manage_stock'          => 'Yes',
            'stock_availability'    => 'In Stock',
            'website_name'          => 'SmokeTestSite',
            'category_name'         => 'st-subcat',
            'grouped_items_sku'     => array('SP-01', 'VP-01', 'DP-01'),
            //<!-- Associated Products tab -->
            'search_product_sku'     => array('SP-01', 'VP-01', 'DP-01')
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}