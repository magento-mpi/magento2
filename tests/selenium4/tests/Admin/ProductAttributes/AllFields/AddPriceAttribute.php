<?php

class Admin_ProductAttributes_AllFields_AddPriceAttribute extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/attributes');
        $this->setUiNamespace();
    }

    /**
     * Test addition new Attribute
     */
    function testPriceAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_price_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Price',
            'default_value_text' => '150',
            'unique_value' => 'Yes',
            'values_required' => 'Yes',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            //<-- Frontend Properties -->
            'use_in_quick_search' => 'Yes',
            'use_in_advanced_search' => 'Yes',
            'comparable' => 'Yes',
            'use_in_layered_navigation' => 'Filterable (with results)',
            'use_in_search_results' => 'Yes',
            'use_for_promo_rules' => 'Yes',
            'position' => 5,
            'visible_on_front' => 'Yes',
            'use_in_product_listing' => 'Yes',
            'use_for_sort_by' => 'Yes',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Price',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'Price(SmokeTestStoreView)',
                'Default Store View' => 'Price(Default Store View)'
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}