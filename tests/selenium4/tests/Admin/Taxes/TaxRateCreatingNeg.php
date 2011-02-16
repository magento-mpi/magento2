<?php

class Admin_Taxes_TaxRteeCreatingNeg extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/tax');
        $this->setUiNamespace();
    }

    /**
     * Test creating Tax Rate
     */
    function testCreateTaxRate() {
        $taxData = array(
        'tax_rate_identifier' => 'US-CA-*-Rate 1',
        'tax_rate_percent' => Core::getEnvConfig('backend/tax/tax_rate_percent'),
        'zip_post_code' => Core::getEnvConfig('backend/tax/zip_post_code'),
        'country' => Core::getEnvConfig('backend/tax/country'),
        'state' => Core::getEnvConfig('backend/tax/state'),
        'tax_rule_priority' => Core::getEnvConfig('backend/tax/tax_rule_priority'),
        'tax_rule_sort_order' => Core::getEnvConfig('backend/tax/tax_rule_sort_order'),
        'store_view_name' => Core::getEnvConfig('backend/scope/store_view/name'),
        'tax_store_view_title' => Core::getEnvConfig('backend/tax/tax_store_view_title'),
            );
        if ($this->model->doLogin()) {
            $this->model->createTaxRate($taxData);
        }
    }

}