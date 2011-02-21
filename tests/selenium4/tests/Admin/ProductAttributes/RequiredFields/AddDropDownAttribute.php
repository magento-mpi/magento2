<?php

class Admin_ProductAttributes_RequiredFields_AddDropDownAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_dropdown_required_fields',
            'attrribute_type' => 'Dropdown',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Dropdown(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}