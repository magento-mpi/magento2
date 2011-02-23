<?php

class Admin_Taxes_CustomerTaxClassDelete extends TestCaseAbstract {

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
     * Test delete Customer Tax Class
     */
    function testDeleteCustomerTaxClass()
    {
        $taxData = array(
            'search_customer_tax_class_name' => 'Test Customer Tax Class',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteTaxElement($taxData, 'customerClass');
        }
    }

}