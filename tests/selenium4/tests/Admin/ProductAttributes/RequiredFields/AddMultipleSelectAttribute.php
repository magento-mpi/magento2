<?php

class Admin_ProductAttributes_RequiredFields_AddMultipleSelectAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_multipleselect_required_fields',
            'attrribute_type' => 'Multiple Select',            
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Multiple Select(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}