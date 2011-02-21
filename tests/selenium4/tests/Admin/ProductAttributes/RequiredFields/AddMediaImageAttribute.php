<?php

class Admin_ProductAttributes_RequiredFields_AddMediaImageAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_mediaimage_required_fields',
            'attrribute_type' => 'Media Image',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Media Image(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}