<?php

class Admin_ProductAttributes_AllFields_AddDropDownAttribute extends TestCaseAbstract {

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
    function testDropDownAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_dropdown_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Dropdown',
            'unique_value' => 'Yes',
            'values_required' => 'Yes',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            'use_to_create_configurable' => 'Yes',
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
            'attribute_admin_title' => 'Dropdown',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'Dropdown(SmokeTestStoreView)',
                'Default Store View' => 'Dropdown(Default Store View)'
            ),
            'attribute_admin_option_name' => array(
                'Drop_Simple',
                'Drop_Virtual',
                'Drop_Download',
                'Drop_Grouped',
                'Drop_Configurable',
                'Drop_Bundle',
                'Drop_Gift Card'
            ),
            'attribute_admin_option_position' => array(1, 2, 3, 4, 5, 6, 7),
            'attribute_store_view_option_name' => array(
                'SmokeTestStoreView' => array(
                    'Drop_Simple(SmokeTestStoreView)',
                    'Drop_Virtual(SmokeTestStoreView)',
                    'Drop_Download(SmokeTestStoreView)',
                    'Drop_Grouped(SmokeTestStoreView)',
                    'Drop_Configurable(SmokeTestStoreView)',
                    'Drop_Bundle(SmokeTestStoreView)',
                    'Drop_Gift Card(SmokeTestStoreView)'
                ),
                'Default Store View' => array(
                    'Drop_Simple(Default Store View)',
                    'Drop_Virtual(Default Store View)',
                    'Drop_Download(Default Store View)',
                    'Drop_Grouped(Default Store View)',
                    'Drop_Configurable(Default Store View)',
                    'Drop_Bundle(Default Store View)',
                    'Drop_Gift Card(Default Store View)'
                ),
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}