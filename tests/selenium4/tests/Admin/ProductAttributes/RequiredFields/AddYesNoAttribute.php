<?php

class Admin_ProductAttributes_RequiredFields_AddYesNoAttribute extends TestCaseAbstract {

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
    function testYesNoAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_yesno_required_fields',
            'attrribute_type' => 'Yes/No',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Yes/No(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}