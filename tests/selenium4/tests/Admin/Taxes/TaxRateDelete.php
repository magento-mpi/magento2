<?php

class Admin_Taxes_TaxRateDelete extends TestCaseAbstract {

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
     * Test delete Tax Rate
     */
    function testDeleteTaxtRate()
    {
        $taxData = array(
            'search_tax_rate_name' => 'Test Tax Rate',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteTaxElement($taxData, 'rate');
        }
    }

}