<?php

class Admin_Taxes_TaxRulecreating extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/tax');
        $this->setUiNamespace();
    }

    /**
     * Test creating Tax Rule
     */
    function testCreateTaxRule() {
        $taxData = array(
        'product_tax_class' => array('Test product tax class 1', 'Test product tax class 2', 'Test product tax class 3'),
            ////Core::getEnvConfig('backend/tax/product_tax_class_name'),
        'customer_tax_class' => array('Test customer tax class 1', 'Test customer tax class 2', 'Test customer tax class 3'),
            ////Core::getEnvConfig('backend/tax/customer_tax_class_name'),
        'tax_rate' => array('Test tax rate 1', 'Test tax rate 2', 'Test tax rate 3'),
            ////Core::getEnvConfig('backend/tax/tax_rate_identifier'),
        'tax_rate_percent' => Core::getEnvConfig('backend/tax/tax_rate_percent'),
        'tax_rule_name' => Core::getEnvConfig('backend/tax/tax_rule_name'),
        'zip_post_code' => Core::getEnvConfig('backend/tax/zip_post_code'),
        'country' => Core::getEnvConfig('backend/tax/country'),
        'state' => Core::getEnvConfig('backend/tax/state'),
        'tax_rule_priority' => Core::getEnvConfig('backend/tax/tax_rule_priority'),
        'tax_rule_sort_order' => Core::getEnvConfig('backend/tax/tax_rule_sort_order'),
        'store_view_name' => Core::getEnvConfig('backend/scope/store_view/name'),
        'tax_store_view_title' => Core::getEnvConfig('backend/tax/tax_store_view_title'),
            );
        if ($this->model->doLogin()) {
            $this->model->createTaxRule($taxData);
        }
    }

}