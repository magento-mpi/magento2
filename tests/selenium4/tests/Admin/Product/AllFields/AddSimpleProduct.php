<?php

class Admin_Product_AllFields_AddSimpleProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Simple Product by filling all default fields
     * on tabs "General", "Prices", "Inventory", "Websites", "Categories"
     */
    function testSimpleProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Simple Product',
            'attribute_set'         => 'smoke_attrSet',
            // <!-- Genral tab -->
            'name'                  => 'Simple Product 02.All Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'SP-02',
            'weight'                => 25,
            'news_from_date'        => '09/20/10',
            'news_to_date'          => '09/30/10',
            'status'                => 'Enabled',
            'url_key'               => '',
            'visibility'            => 'Catalog',
            'allow_gift_message'    => 'Yes',
            // <!-- Prices tab -->
            'price'                 => 90,
            'special_price'         => 85,
            'special_from_date'     => '09/20/10',
            'special_to_date'       => '09/30/10',
            'tier_price_price'      => array(80, 82),
            'tier_price_qty'        => array(20, 10),
            'tax_class'             => 'None',
            'enable_googlecheckout' => 'No',
            // <!-- Inventory tab -->
            'manage_stock'          => 'Yes',
            'inventory_qty'         => '1000',
            'inventory_min_qty'     => 2,
            'min_sale_qty'          => 1,
            'max_sale_qty'          => 100,
            'is_qty_decimal'        => 'Yes',
            'backorders'            => 'Allow Qty Below 0',
            'notify_stock_qty'      => 5,
            'enable_qty_increments' => 'Yes',
            'qty_increments'        => 2,
            'stock_availability'    => 'In Stock',
            // <!-- Website tab -->
            'website_name'          => 'SmokeTestSite',
            // <!-- Category tab -->
            'category_name'         => 'st-subcat'
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}