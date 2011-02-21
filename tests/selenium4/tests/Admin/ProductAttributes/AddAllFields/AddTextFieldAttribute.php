<?php

class Admin_ProductAttributes_AllFields_AddTextFieldAttribute extends TestCaseAbstract {

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
    function testTextFieldAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_text_field_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Text Field',
            'default_value_text' => 'Text Field',
            'unique_value' => 'Yes',
            'values_required' => 'Yes',
            'input_validation' => 'Decimal Number',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            //<-- Frontend Properties -->
            'use_in_quick_search' => 'Yes',
            'use_in_advanced_search' => 'Yes',
            'comparable' => 'Yes',
            'use_for_promo_rules' => 'Yes',
            'html_allowed_on_front' => 'Yes',
            'visible_on_front' => 'Yes',
            'use_in_product_listing' => 'Yes',
            'use_for_sort_by' => 'Yes',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Text Field',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'SmokeTestStoreView',
                'Default Store View' => 'Default Store View'
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}