<?php

class Admin_Product_AllFields_AddConfigurableProduct extends TestCaseAbstract {

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
     * Test Case: Creation new Configurable Product by filling all default fields
     * on tabs "General", "Prices", "Inventory", "Websites", "Categories" and "Associated Products"
     */
    function testConfigurableProductCreation()
    {
        $arrayData = array(
            'type'                  => 'Configurable Product',
            'attribute_set'         => 'smoke_attrSet',
            'attrib_for_conf_prod'  => 'Dropdown',
            // <!-- Genral tab -->
            'name'                  => 'Configurable Product 02.All Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'CP-02',
            'news_from_date'        => '09/20/10',
            'news_to_date'          => '09/30/10',
            'status'                => 'Enabled',
            'url_key'               => '',
            'visibility'            => 'Catalog',
            'allow_gift_message'    => 'Yes',
            // <!-- Prices tab -->
            'price'                 => 200,
            'special_price'         => 185,
            'special_from_date'     => '09/20/10',
            'special_to_date'       => '09/30/10',
            'tier_price_price'      => array(180, 182),
            'tier_price_qty'        => array(20, 10),
            'tax_class'             => 'None',
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
            'search_product_sku'=> array('SP-01', 'VP-01', 'DP-01')

        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}