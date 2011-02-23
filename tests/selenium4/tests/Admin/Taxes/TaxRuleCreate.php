<?php

class Admin_Taxes_TaxRuleCreate extends TestCaseAbstract {

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
     * Test creating Tax Rule
     */
    function testCreateTaxRule()
    {
        $taxData = array(
            'tax_rule_name'         => 'Test Tax Rule',
            'customer_tax_class'    => array('Test Customer Tax Class'),
            'product_tax_class'     => array('Test Product Tax Class'),
            'tax_rate'              => array('Test Tax Rate'),
            'tax_rule_priority'     => 1,
            'tax_rule_sort_order'   => 1
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateTaxRule($taxData);
        }
    }

}