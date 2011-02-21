<?php

class Admin_Product_AllFields_AddVirtualProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Virtual Product by filling all default fields
     * on tabs "General", "Prices", "Inventory", "Websites", "Categories"
     */
    function testVirtualProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Virtual Product',
            'attribute_set'         => 'smoke_attrSet',
            // <!-- Genral tab -->
            'name'                  => 'Virtual Product 02.All Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'VP-02',
            'news_from_date'        => '09/20/10',
            'news_to_date'          => '09/30/10',
            'status'                => 'Enabled',
            'url_key'               => '',
            'visibility'            => 'Catalog',
            'allow_gift_message'    => 'Yes',
            // <!-- Prices tab -->
            'price'                 => 100,
            'special_price'         => 90,
            'special_from_date'     => '09/20/10',
            'special_to_date'       => '09/30/10',
            'tier_price_price'      => array(85, 87),
            'tier_price_qty'        => array(10, 5),
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