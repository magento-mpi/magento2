<?php

class Admin_Product_RequiredFields_AddGiftCard extends TestCaseAbstract {

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
     * Test Case: Creation new Gift Card by filling required fields
     */
    function testGiftCardCreation()
    {
        $arrayData = array(
            'type'                  => 'Gift Card',
            'attribute_set'         => 'smoke_attrSet',
            'name'                  => 'Gift Card 01(Open Amount).Required Fields',
            'description'           => 'Product description',
            'short_description'     => 'Product short description',
            'sku'                   => 'GC-01',
            'status'                => 'Enabled',
            'allow_open_amount'     => 'Yes',
            'open_amount_min'       => rand(100, 200),
            'open_amount_max'       => rand(200, 300),
            'manage_stock'          => 'Yes',
            'inventory_qty'         => '100',
            'stock_availability'    => 'In Stock',
            'website_name'          => 'SmokeTestSite',
            'category_name'         => 'st-subcat'
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProduct($arrayData);
        }
    }

}