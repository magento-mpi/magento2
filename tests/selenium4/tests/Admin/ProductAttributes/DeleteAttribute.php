<?php

class Admin_ProductAttributes_DeleteAttribute extends TestCaseAbstract {

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
    function testAttributeDeletion()
    {
        $Data = array(
            'search_product_attribute_code' => 'attrribute_date_all_fields',
            'search_product_attribute_label' => 'Date',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteAttribute($Data);
        }
    }

}