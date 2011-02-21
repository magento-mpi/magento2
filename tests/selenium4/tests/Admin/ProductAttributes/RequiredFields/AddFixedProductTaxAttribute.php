<?php

class Admin_ProductAttributes_RequiredFields_AddFixedProductTaxAttribute extends TestCaseAbstract {

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
            'attrribute_code' => 'attrribute_fixedproducttax_required_fields',
            'attrribute_type' => 'Fixed Product Tax',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Fixed Product Tax(RequiredFields)',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}