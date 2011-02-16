<?php

class Admin_Taxes_CustomerTaxClassCreating extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/tax');
        $this->setUiNamespace();
    }

    /**
     * Test creating Customer Tax Class
     */
    function testCreateCustomerTaxClass() {
        $taxData = array(
        'customer_tax_class_name' => Core::getEnvConfig('backend/tax/customer_tax_class_name/ctc1'),
            );
        if ($this->model->doLogin()) {
            $this->model->createCustomerTaxClass($taxData);
        }
    }

}