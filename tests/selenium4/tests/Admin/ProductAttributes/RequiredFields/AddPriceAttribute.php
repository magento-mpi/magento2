<?php

class Admin_ProductAttributes_RequiredFields_AddPriceAttribute extends TestCaseAbstract {

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
    function testPriceAttributeCreation()
    {
        $arrayData = array(
            //<-- Attribute Properties -->
            'attrribute_code' => 'attrribute_price_required_fields',
            'attrribute_type' => 'Price',
            //<-- Manage Titles (Size, Color, etc.) -->
            'attribute_admin_title' => 'Price(RequiredFields)',

        );
        if ($this->model->doLogin()) {
            $this->model->doCreateAttribute($arrayData);
        }
    }

}