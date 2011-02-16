<?php

class Admin_Taxes_unifiedTaxDelete extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/TaxRuleDelete');
        $this->setUiNamespace();
    }

    /**
     * Test deleating Tax Rule
     */
    function UnifiedTaxDeleating() {
        $taxData = array(
        'product_tax_class_name' => Core::getEnvConfig('backend/tax/product_tax_class_name'),
        'customer_tax_class_name' => Core::getEnvConfig('backend/tax/customer_tax_class_name'),
        'tax_rate_identifier' => Core::getEnvConfig('backend/tax/tax_rate_identifier'),
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
            $this->model->unifiedTaxDelete($taxData,"manage_tax_zone_rate","tax_rate_identifier");
            /*
             * manage_tax_zone_rate, tax_rate_identifier
             * manage_tax_rules, tax_rule_name
             * product_tax_classes, product_tax_class_name
             * customer_tax_classes, customer_tax_class_name
             */
        }
    }
}