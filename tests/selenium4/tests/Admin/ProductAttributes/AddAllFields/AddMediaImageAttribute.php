<?php

class Admin_ProductAttributes_AllFields_AddMediaImageAttribute extends TestCaseAbstract {

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
    function testMediaImageAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_mediaimage_all_fields',
            'scope' => 'Global',
            'attrribute_type' => 'Media Image',
            'apply_to' => 'All Product Types', //'Selected Product Types',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Media Image',
            'attribute_store_view_title' => array(
                'SmokeTestStoreView' => 'Media Image(SmokeTestStoreView)',
                'Default Store View' => 'Media Image(Default Store View)'
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}