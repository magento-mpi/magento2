<?php

class Admin_Product_AllFields_AddDownloadableProductNo extends TestCaseAbstract {

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
     * Test Case: Creation new Downloadable Product by filling all default fields
     * on tabs "General", "Prices", "Inventory", "Websites", "Categories" and "Downloadable Information"
     */
    function testDownloadableProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Downloadable Product',
            'attribute_set'         => 'smoke_attrSet',
            // <!-- Genral tab -->
            'name'                  => 'Downloadable Product 02(Purchase=No).All Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'DP-02',
            'news_from_date'        => '09/20/10',
            'news_to_date'          => '09/30/10',
            'status'                => 'Enabled',
            'url_key'               => '',
            'visibility'            => 'Catalog',
            'allow_gift_message'    => 'Yes',
            // <!-- Prices tab -->
            'price'                 => 290,
            'special_price'         => 285,
            'special_from_date'     => '09/20/10',
            'special_to_date'       => '09/30/10',
            'tier_price_price'      => array(280, 282),
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
            'category_name'         => 'st-subcat',
            // <!-- Downloadable Information tab -->
            'downloadable_samples_title'            => 'samples_title',
            'downloadable_sample_title'             => 'sample_title_1',
            'downloadable_sample_url'               => 'http://sample_url_1',
            'downloadable_sample_sort_order'        => 1,
            'downloadable_links_title'              => 'links_title',
            'downloadable_links_purchase_type'       => 'No',
            'downloadable_link_title'               => 'link_title_1',
            'downloadable_link_max_downloads'       => 5,
            'downloadable_link_shareable'           => 'No',
            'downloadable_link_sample_url'          => 'http://link_sample_url_1',
            'downloadable_link_url'                 => 'http://link_url_1',
            'downloadable_link_sort_order'          => 1

        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}