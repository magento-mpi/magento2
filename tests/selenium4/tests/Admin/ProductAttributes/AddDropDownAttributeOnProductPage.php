<?php

class Admin_ProductAttributes_AddDropDownAttributeOnProductPage extends TestCaseAbstract {

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
     * Test addition new Attribute on product Page
     */
    function testDropDownAttributeCreationOnProductPage()
    {
        $arrayData = array(
            'attribute_set' =>'smoke_attrSet',
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_dropdown_for_conf_product',
            'scope' => 'Global',
            'attrribute_type' => 'Dropdown',
            'values_required' => 'Yes',
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
            'attribute_admin_option_name' => array(
                'Simple Product',
                'Virtual Product',
                'Downloadable Product',
                'Grouped Product',
                'Configurable Product',
                'Bundle Product',
                'Gift Card'
            ),
            'attribute_admin_option_position' => array(1, 2, 3, 4, 5, 6, 7),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttributeOnProductPage($arrayData);
        }
    }

}