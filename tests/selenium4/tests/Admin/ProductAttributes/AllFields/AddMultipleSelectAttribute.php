<?php

class Admin_ProductAttributes_AllFields_AddMultipleSelectAttribute extends TestCaseAbstract {

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
    function tesMultipleSelecttAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_multipleselect_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Multiple Select',
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
            'position' => 4,
            'visible_on_front' => 'Yes',
            'use_in_product_listing' => 'Yes',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Multiple Select',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'Multiple Select(SmokeTestStoreView)',
                'Default Store View' => 'Multiple Select(Default Store View)'
            ),
            'attribute_admin_option_name' => array(
                'MS_Simple',
                'MS_Virtual',
                'MS_Download',
                'MS_Grouped',
                'MS_Configurable',
                'MS_Bundle',
                'MS_Gift Card'
            ),
            'attribute_admin_option_position' => array(1, 2, 3, 4, 5, 6, 7),
            'attribute_store_view_option_name' => array(
                'SmokeTestStoreView' => array(
                    'MS_Simple(SmokeTestStoreView)',
                    'MS_Virtual(SmokeTestStoreView)',
                    'MS_Download(SmokeTestStoreView)',
                    'MS_Grouped(SmokeTestStoreView)',
                    'MS_Configurable(SmokeTestStoreView)',
                    'MS_Bundle(SmokeTestStoreView)',
                    'MS_Gift Card(SmokeTestStoreView)'
                ),
                'Default Store View' => array(
                    'MS_Simple(Default Store View)',
                    'MS_Virtual(Default Store View)',
                    'MS_Download(Default Store View)',
                    'MS_Grouped(Default Store View)',
                    'MS_Configurable(Default Store View)',
                    'MS_Bundle(Default Store View)',
                    'MS_Gift Card(Default Store View)'
                ),
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}