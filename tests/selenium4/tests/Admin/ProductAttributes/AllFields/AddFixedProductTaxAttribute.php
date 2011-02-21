<?php

class Admin_ProductAttributes_AllFields_AddFixedProductTaxAttribute extends TestCaseAbstract {

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
    function testFixedProductTaxAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_fixedproducttax_all_fields',
            'attrribute_type' => 'Fixed Product Tax',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Fixed Product Tax',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'Fixed Product Tax(SmokeTestStoreView)',
                'Default Store View' => 'Fixed Product Tax(Default Store View)'
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}