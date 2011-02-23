<?php

class Admin_Taxes_TaxRuleDelete extends TestCaseAbstract {

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
     * Test delete Tax Rule
     */
    function testDeleteTaxtRule()
    {
        $taxData = array(
            'search_tax_rule_name' => 'Test Tax Rule',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteTaxElement($taxData, 'rule');
        }
    }

}