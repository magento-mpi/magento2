<?php

class Admin_Taxes_ProductTaxClassCreatingNeg extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/tax');
        $this->setUiNamespace();
    }

    /**
     * Test creating Product Tax Class
     */
    function testCreateProductTaxClass() {
        $taxData = array(
        'product_tax_class_name' => 'Shipping',
            );
        if ($this->model->doLogin()) {
            $this->model->createProductTaxClass($taxData);
        }
    }

}