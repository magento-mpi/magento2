<?php

class Admin_Taxes_TaxRulecreatingNeg extends TestCaseAbstract {

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
        'product_tax_class_name' => Core::getEnvConfig('backend/tax/product_tax_class_name'),
        'customer_tax_class_name' => Core::getEnvConfig('backend/tax/customer_tax_class_name'),
        'tax_rate_identifier' => Core::getEnvConfig('backend/tax/tax_rate_identifier'),
        'tax_rate_percent' => Core::getEnvConfig('backend/tax/tax_rate_percent'),
        'tax_rule_name' => 'Retail Customer-Taxable Goods-Rate 1',
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