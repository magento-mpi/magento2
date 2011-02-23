<?php

class Admin_Taxes_ProductTaxClassCreate extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/tax');
        $this->setUiNamespace();
    }

    /**
     * Test creating Product Tax Class
     */
    function testCreateProductTaxClass()
    {
        $taxData = array(
            'product_tax_class_name' => 'Test Product Tax Class',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateProductTaxClass($taxData);
        }
    }

}