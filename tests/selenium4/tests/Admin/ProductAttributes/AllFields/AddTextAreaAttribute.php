<?php

class Admin_ProductAttributes_AllFields_AddTextAreaAttribute extends TestCaseAbstract {

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
    function testTextAreaAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_text_area_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Text Area',
            'default_value_textarea' => 'Text Area',
            'unique_value' => 'Yes',
            'values_required' => 'Yes',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            //<-- Frontend Properties -->
            'use_in_quick_search' => 'Yes',
            'use_in_advanced_search' => 'Yes',
            'comparable' => 'Yes',
            'use_for_promo_rules' => 'Yes',
            'wysiwyg_enable' => 'Yes',
            //'html_allowed_on_front' => 'Yes',
            'visible_on_front' => 'Yes',
            'use_in_product_listing' => 'Yes',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Text Area',
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