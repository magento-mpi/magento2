<?php

class Admin_ProductAttributes_AllFields_AddDateAttribute extends TestCaseAbstract {

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
    function testDateAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_date_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Date',
            'default_value_date' => '11/25/2010',
            'unique_value' => 'Yes',
            'values_required' => 'Yes',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            //<-- Frontend Properties -->
            'use_in_quick_search' => 'Yes',
            'use_in_advanced_search' => 'Yes',
            'comparable' => 'Yes',
            'use_for_promo_rules' => 'Yes',
            'visible_on_front' => 'Yes',
            'use_in_product_listing' => 'Yes',
            'use_for_sort_by' => 'Yes',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Date',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'Date(SmokeTestStoreView)',
                'Default Store View' => 'Date(Default Store View)'
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}