<?php

class Admin_ProductAttributes_RequiredFields_AddTextFieldAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_text_field_required_fields',
            'attrribute_type' => 'Text Field',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Text Field(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}