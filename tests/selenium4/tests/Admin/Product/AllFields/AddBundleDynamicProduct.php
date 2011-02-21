<?php

class Admin_Product_AllFields_AddBundleDynamicProduct extends TestCaseAbstract {

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
     * est Case: Creation new Bundle.Dynamic Product by filling required fields
     */
    function testBundleDynamicProductCreation()
    {
        $arrayData = array(
            'type'                      => 'Bundle Product',
            'attribute_set'             => 'smoke_attrSet',
            // <!-- Genral tab -->
            'name'                      => 'Bundle Product 03(Dynamic).All Fields',
            'description'               => 'Product description',
            'short_description'         => 'Product short description',
            'sku_type'                  => 'Dynamic',
            'sku'                       => 'BP-03',
            'weight_type'               => 'Dynamic',
            'news_from_date'            => '09/20/10',
            'news_to_date'              => '09/30/10',
            'status'                    => 'Enabled',
            'url_key'                   => '',
            'visibility'                => 'Catalog',
            'allow_gift_message'        => 'Yes',
            // <!-- Prices tab -->
            'price_type'                => 'Dynamic',
            'special_price'             => 90,
            'special_from_date'         => '09/20/10',
            'special_to_date'           => '09/30/10',
            'tier_price_price'          => array(80, 85),
            'tier_price_qty'            => array(20, 10),
            'tax_class'                 => 'None',
            'enable_googlecheckout'     => 'No',
            'price_view'                => 'As Low as',
            // <!-- Inventory tab -->
            'manage_stock'              => 'Yes',
            'enable_qty_increments'     => 'Yes',
            'qty_increments'            => 2,
            'stock_availability'        => 'In Stock',
            // <!-- Website tab -->
            'website_name'              => 'SmokeTestSite',
            // <!-- Category tab -->
            'category_name'             => 'st-subcat',
            // <!-- Bundle Items Tab-->
            'bundle_shipment_type'      => 'Separately',
            'bundle_options_title'      => 'Bundle Options Title 01',
            'bundle_options_position'   => '1',
            'bundle_options_type'       => 'Checkbox',
            'bundle_options_required'   => 'No',
            'search_product_sku'        => array('SP-01', 'VP-01', 'DP-01')
        );

        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}