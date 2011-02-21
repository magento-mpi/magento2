<?php

class Admin_ProductAttributes_RequiredFields_AddTextAreaAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_text_area_required_fields',
            'attrribute_type' => 'Text Area',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Text Area(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}