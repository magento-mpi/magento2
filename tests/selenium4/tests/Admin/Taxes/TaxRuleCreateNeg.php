<?php

class Admin_Taxes_TaxRuleCreateNeg extends TestCaseAbstract {

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
            'tax_rule_name'         => 'Retail Customer-Taxable Goods-Rate 1',
            'customer_tax_class'    => array('Retail Customer'),
            'product_tax_class'     => array('Taxable Goods'),
            'tax_rate'              => array('US-CA-*-Rate 1'),
        );

        if ($this->model->doLogin()) {
            $this->model->createTaxRule($taxData);
        }
    }

}