<?php

class Admin_Taxes_CustomerTaxClassCreateNeg extends TestCaseAbstract {

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
     * Test creating Customer Tax Class
     */
    function testCreateCustomerTaxClass()
    {
        $taxData = array(
            'customer_tax_class_name' => 'Retail Customer',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateCustomerTaxClass($taxData);
        }
    }

}