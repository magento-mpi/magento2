<?php

class Admin_ProductAttributes_RequiredFields_AddDateAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_date_required_fields',
            'attrribute_type' => 'Date',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Date(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}