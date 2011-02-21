<?php

class Admin_Product_AllFields_AddGroupedProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Grouped Product by filling all default fields
     * on tabs "General", "Prices", "Inventory", "Websites", "Categories"
     */
    function testGroupedProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Grouped Product',
            'attribute_set'         => 'smoke_attrSet',
            // <!-- Genral tab -->
            'name'                  => 'Grouped Product 02.All Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'GP-02',
            'news_from_date'        => '09/20/10',
            'news_to_date'          => '09/30/10',
            'status'                => 'Enabled',
            'url_key'               => '',
            'visibility'            => 'Catalog',
            'allow_gift_message'    => 'Yes',
            // <!-- Prices tab -->
            'enable_googlecheckout' => 'No',
            // <!-- Inventory tab -->
            'manage_stock'          => 'Yes',
            'enable_qty_increments' => 'Yes',
            'qty_increments'        => 2,
            'stock_availability'    => 'In Stock',
            // <!-- Website tab -->
            'website_name'          => 'SmokeTestSite',
            // <!-- Category tab -->
            'category_name'         => 'st-subcat',
            //<!-- Associated Products tab -->
            'search_product_sku'     => array('SP-01', 'VP-01', 'DP-01')
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}