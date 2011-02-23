<?php

class Admin_Taxes_ProductTaxClassDelete extends TestCaseAbstract {

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
     * Test delete Product Tax Class
     */
    function testDeleteProductTaxClass()
    {
        $taxData = array(
            'search_product_tax_class_name' => 'Test Product Tax Class',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteTaxElement($taxData, 'productClass');
        }
    }

}