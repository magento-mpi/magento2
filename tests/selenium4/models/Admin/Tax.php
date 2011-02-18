<?php

/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Tax extends Model_Admin {

    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->taxData = array();
    }

    /**
     * creating Product Tax Class
     *
     * @param array $params May contain the following params:
     * product_tax_class_name
     *
     */
    public function createProductTaxClass($params)
    {
        $this->printDebug('createProductTaxClass() started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/manage_product_tax_class/edit_product_tax_class_page');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_product_tax_class"));
        $this->clickAndWait($this->getUiElement("/admin/pages/sales/tax/manage_product_tax_class/buttons/add_new"));
        $this->type($this->getUiElement("inputs/product_tax_class_name"), $taxData["product_tax_class_name"]);
        $this->saveAndVerifyForErrors();
    }

    /**
     * creating Customer Tax Class
     *
     * @param array $params May contain the following params:
     * customer_tax_class_name
     *
     */
    public function createCustomerTaxClass($params)
    {
        $result = true;
        $this->printDebug('createCustomerTaxClass() started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/manage_customer_tax_class/edit_customer_tax_class_page');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_customer_tax_class"));
        $this->clickAndWait($this->getUiElement("/admin/pages/sales/tax/manage_customer_tax_class/buttons/add_new"));
        $this->type($this->getUiElement("inputs/customer_tax_class_name"), $taxData["customer_tax_class_name"]);
        $this->saveAndVerifyForErrors();
    }

    /*
     * verifiens is store_view_name for tax rate titles is array
     */

    public function isTaxTitlesArray($storeViewTaxTitles)
    {
        if (is_array($storeViewTaxTitles)) {
            $qtyStores = count($storeViewTaxTitles);
            for ($y = 0; $y < $qtyStores; $y++) {
                $this->fillTaxTitles($storeViewTaxTitles[$y]);
            }
        } elseif ($storeViewTaxTitles != Null) {
            $this->fillTaxTitles($storeViewTaxTitles);
        } else {
            $this->printInfo("Tax identifier was used for all Tax Titles");
        }
    }

    /*
     * fill tax rate title for store view
     */

    public function fillTaxTitles($storeViewTaxTitles)
    {
        $qtyStore = $this->getXpathCount($this->getUiElement("elements/store_view_name_for_tax_title"));
        for ($i = 1; $i <= $qtyStore; $i++) {
            $storeName = $this->getText($this->getUiElement("elements/store_view_name_for_title_many", $i));
            $this->printInfo($storeName);
            if ($storeName == $storeViewTaxTitles) {
                $this->printInfo($i);
                $needStore = $i;
            }
        }
        if ($needStore != NULL) {
            $this->type($this->getUiElement("inputs/store_view_name_for_tax_title", $needStore),
                    $storeViewTaxTitles);
        }
    }

    /**
     * creating Tax Rate
     *
     * @param array $params May contain the following params:
     * tax_rate_identifier, tax_rate_percent, zip_post_code,
     * country, state, store_view_name, store_view_name_for_title_many
     * store_view_name_for_tax_title, tax_store_view_title
     * $qtyStore, $i, $storeName, $needStore
     *
     */
    public function createTaxRate($params)
    {
        $storeViewTaxTitles = $this->isSetValue($params, 'store_view_name');

        $this->printDebug('createTaxRate() started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_zone_rate');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_tax_zone_rate"));
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_zone_rate/edit_tax_zone_rate_page');
        $this->type($this->getUiElement("inputs/tax_rate_identifier"), $taxData["tax_rate_identifier"]);
        $this->type($this->getUiElement("inputs/tax_rate_percent"), $taxData["tax_rate_percent"]);
        //specifeing zip code or zip code range.
        if ($taxData["zip_is_range"] == 'Yes') {
            $this->printInfo($taxData["zip_is_range"]);
            $this->select($this->getUiElement("selectors/zip_post_is_range"), 'label=Yes');
            $this->type($this->getUiElement("inputs/range_from"), $taxData["zip_range_from"]);
            $this->type($this->getUiElement("inputs/range_to"), $taxData["zip_range_to"]);
        } elseif ($this->getUiElement($taxData["zip_is_range"] == 'No')) {
            $this->type($this->getUiElement("inputs/zip_post_code"), $taxData["zip_post_code"]);
        } else {
            $this->printInfo("Zip is Range selector defined incorect so we use default value No");
            $this->type($this->getUiElement("inputs/zip_post_code"), $taxData["zip_post_code"]);
        }
        //select country
        $this->select($this->getUiElement("selectors/country"), $taxData["country"]);
        //verifieng if country have states
        if (!$this->isElementPresent($this->getUiElement("selectors/state_disabled"))) {
            $this->select($this->getUiElement("selectors/state"), $taxData["state"]);
        } else {
            $this->printInfo("There are no states to select for this country");
        }
        //fill tax titles
        $this->isTaxTitlesArray($storeViewTaxTitles);
        //save tax rate
        $this->saveAndVerifyForErrors();
    }

    /*
     * verifeing if element is array
     *
     */

    public function multiselectTaxElements($taxElementMultyselect, $pathMultiselect)
    {
        if (is_array($taxElementMultyselect)) {
            $qtyStores = count($taxElementMultyselect);
            for ($y = 0; $y < $qtyStores; $y++) {
                $this->selectElement($taxElementMultyselect[$y], $pathMultiselect);
            }
        } elseif ($taxElementMultyselect != Null) {
            $this->selectElement($taxElementMultyselect, $pathMultiselect);
        } else {
            $this->printInfo('Variable ' . $pathMultiselect . ' is not defined');
        }
    }

    /*
     * select element in the multiselect
     * 
     */

    public function selectElement($elementName, $pathMultiselect)
    {
        if ($this->isElementPresent($this->getUiElement('selectors/' . $pathMultiselect) .
                        "//option[contains(.,'" . $elementName . "')]")) {
            $this->addSelection($this->getUiElement('selectors/' . $pathMultiselect), "label=regexp:" . $elementName);
        } else {
            $this->printInfo("Element _" . $elementName . "_ is defined wrong");
        }
    }

    /**
     * creating Tax Rule
     *
     * @param array $params May contain the following params:
     * tax_rule_name, customer_tax_class_name, product_tax_class_name,
     * tax_rate_identifier, tax_rule_priority, tax_rule_sort_order
     *
     */
    public function createTaxRule($params)
    {
        $this->printDebug('createCustomerTaxClass() started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_rule');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_tax_rule"));
        $this->clickAndWait($this->getUiElement("buttons/add_new_tax_rule"));
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_rule/edit_tax_rule_page');
        $this->type($this->getUiElement("inputs/tax_rule_name"), $taxData["tax_rule_name"]);
        //select product tax classes
        $taxElementMultyselect = $this->isSetValue($params, 'product_tax_class');
        $pathMultiselect = 'product_tax_class';
        $this->multiselectTaxElements($taxElementMultyselect, $pathMultiselect);
        //select customer tax classes
        $taxElementMultyselect = $this->isSetValue($params, 'customer_tax_class');
        $pathMultiselect = 'customer_tax_class';
        $this->multiselectTaxElements($taxElementMultyselect, $pathMultiselect);
        //select tax rates
        $taxElementMultyselect = $this->isSetValue($params, 'tax_rate');
        $pathMultiselect = 'tax_rate';
        $this->multiselectTaxElements($taxElementMultyselect, $pathMultiselect);
        //input other data
        $this->type($this->getUiElement("inputs/tax_rule_priority"), $taxData["tax_rule_priority"]);
        $this->type($this->getUiElement("inputs/tax_rule_sort_order"), $taxData["tax_rule_sort_order"]);
        //save tax rule
        $this->saveAndVerifyForErrors();
    }

    /**
     * Unified deleating process for all tax elements.
     */
    public function unifiedTaxDelete($params, $path, $dataname)
    {
        $this->printDebug('delete process started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/manage_' . $path);
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_" . $path));
        switch ($path) {
            case "product_tax_class":
                $delete = '_product';
                break;
            case "customer_tax_class":
                $delete = '_customer';
                break;
            case "tax_rule":
                $delete = '_rule';
                break;
            case "tax_zone_rate":
                $delete = '_rate';
                break;
        }
        foreach ($taxData as $key => $value) {
            if (preg_match('/^search_tax' . $delete . '/', $key)) {
                $searchTax[$key] = $value;
            }
        }
        $tax_result = $this->searchAndDoAction('tax_grid_container', $searchTax, 'open', NULL);
        if ($tax_result) {
            $this->setUiNamespace('admin/pages/sales/tax/manage_' . $path . '/edit_' . $path . '_page');
            $this->waitForElement($this->getUiElement("buttons/delete"), 2);
            $this->clickAndWait($this->getUiElement("buttons/delete"));
            if ($this->assertConfirmationPresent('Are you sure you want to do this?')) {
                $this->chooseOkOnNextConfirmation();
            } else {
                $this->printInfo('An error was accured during deleting process');
            }
        }
        // wait for any message
        if ($this->waitForElement($this->getUiElement("/admin/messages/message"), 60)) {
            //check for error message
            if ($this->waitForElement($this->getUiElement("/admin/messages/error"), 2)) {
                $etext = $this->getText($this->getUiElement("/admin/messages/error"));
                $this->setVerificationErrors("Check 1:Deleting process error. " . $etext);
            } else {
                // Check for success message
                if (!$this->waitForElement($this->getUiElement("/admin/messages/success"), 60)) {
                    $this->setVerificationErrors("Check 2: Deleting process error. no success message");
                } else {
                    $this->printInfo('Tax element has been deleted');
                }
            }
        } else {
            $this->setVerificationErrors("Check 3: Deleting process error. No any messages from Magento. Hangs up ?");
        }
        $this->printDebug('Delete process finished');
    }

}