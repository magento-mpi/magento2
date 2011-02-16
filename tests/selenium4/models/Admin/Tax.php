<?php
/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Tax extends Model_Admin
{
     public function loadConfigData()
     {
        parent::loadConfigData();
       $this-> taxData = array();
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
        $this->setUiNamespace('admin/pages/sales/tax/product_tax_classes');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/product_tax_classes"));
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        $this->type($this->getUiElement("inputs/product_tax_class_name"),$taxData["product_tax_class_name"]);
        $this->model->unifiedTaxSave();
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
        $this->setUiNamespace('admin/pages/sales/tax/customer_tax_classes');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/customer_tax_classes"));
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        $this->type($this->getUiElement("inputs/customer_tax_class_name"),$taxData["customer_tax_class_name"]);
        $this->model->unifiedTaxSave();
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
        $this->printDebug('createTaxRate() started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_zone_rate');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_tax_zone_rate"));
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        $this->type($this->getUiElement("inputs/tax_rate_identifier"),$taxData["tax_rate_identifier"]);
        $this->type($this->getUiElement("inputs/tax_rate_percent"),$taxData["tax_rate_percent"]);
        $this->type($this->getUiElement("inputs/zip_post_code"),$taxData["zip_post_code"]);
        $this->select($this->getUiElement("selectors/country"),$taxData["country"]);
        if (($taxData["country"] == 'United States')||($taxData["country"] == 'Canada')) {
        $this->select($this->getUiElement("selectors/state"),$taxData["state"]);
        } 
        $needStore = 0;
        if (isset($taxData['store_view_name'])) {
            $qtyStore = $this->getXpathCount($this->getUiElement("elements/store_view_name_for_tax_title"));
            for ($i = 1; $i <= $qtyStore; $i++) {
                $storeName = $this->getText($this->getUiElement("elements/store_view_name_for_title_many", $i));
                if ($storeName == $taxData['store_view_name']) {
                    $needStore = $i;
                }
            }
            if ($needStore != NULL) {
                $this->type($this->getUiElement("inputs/store_view_name_for_tax_title", $needStore), $taxData["tax_store_view_title"]);
            }
        }    
        $this->model->unifiedTaxSave();
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
        $this->setUiNamespace('admin/pages/sales/tax/manage_tax_rules');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/manage_tax_rules"));
        $this->clickAndWait($this->getUiElement("buttons/add_new_tax_rule"));
        $this->type($this->getUiElement("inputs/tax_rule_name"),$taxData["tax_rule_name"]);
        if (count($taxData["product_tax_class_name"]) <= 1) {
               $this->printDebug('There are no product tax classes');
        } else {
            for ($i = 0; $i < count($taxData["product_tax_class_name"]); $i++) {
                $index = (array_values($taxData["product_tax_class_name"]));
                $this->addSelection($this->getUiElement("selectors/product_tax_class"),"label=".$index[$i]);
            }
        }
        if (count($taxData["customer_tax_class_name"]) <= 1) {
                $this->printDebug('There are no customer tax classes');
        } else {
            for ($i = 0; $i < count($taxData["customer_tax_class_name"]); $i++) {
                $index = (array_values($taxData["customer_tax_class_name"]));
                $this->addSelection($this->getUiElement("selectors/customer_tax_class"),"label=".$index[$i]);
            }
        }
        if (count($taxData["tax_rate_identifier"]) <= 1) {
               $this->printDebug('There are no tax rates');
        } else {
            for ($i = 0; $i < count($taxData["tax_rate_identifier"]); $i++) {
                $index = (array_values($taxData["tax_rate_identifier"]));
                $this->addSelection($this->getUiElement("selectors/tax_rate"),"label=".$index[$i]);
            }
        }
        $this->type($this->getUiElement("inputs/tax_rule_priority"),$taxData["tax_rule_priority"]);
        $this->type($this->getUiElement("inputs/tax_rule_sort_order"),$taxData["tax_rule_sort_order"]);
        $this->model->unifiedTaxSave();
    }

    /**
     * Unified deleating process for all tax elements.
     */
        public function unifiedTaxDelete($params, $path, $datanme)
    {
        $this->printDebug('delete process started...');
        $taxData = $params ? $params : $this->taxData;
        $this->setUiNamespace('admin/pages/sales/tax/'.$path);
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/tax/".$path));
        $this->type($this->getUiElement("inputs/search_line"), $taxData[$dataname]);
        $this->clickAndWait($this->getUiElement("buttons/search"));
        if($this->isTextPresent('No record found.')) {
            $this->printInfo('No such element found');
        } else {
            $this->clickAndWait($this->getUiElement("elements/searched_item", $taxData[$dataname]));
            $this->clickAndWait($this->getUiElement("buttons/delete"));
            if($this->assertConfirmationPresent('Are you sure you want to do this?')) {
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
                    $this->printInfo('' . $taxData[$dataname] . ' has been deleted');
                }
            }
        } else {
            $this->setVerificationErrors("Check 3: Deleting process error. No any messages from Magento. Hangs up ?");
        }
        $this->printDebug('Delete process finished');
    }

    /**
     * Unified saving process for all tax elements.
     */
        public function unifiedTaxSave()
    {
        $this->printDebug('Trying to save tax');
        $this->click($this->getUiElement("buttons/save"));
        if ($this->waitForElement($this->getUiElement("elements/required_field_error"), 1)) {
            $qtyFields = $this->getXpathCount($this->getUiElement("elements/required_failed_qty"));
            for ($i = 1; $i <= $qtyFields; $i++) {
                $selectName = $this->getText($this->getUiElement("elements/required_failed_name", $i));
                $this->printDebug('Field "'.$selectName .'" is required');
            }
        } elseif ($this->waitForElement($this->getUiElement("/admin/messages/message"), 60)) {
                //check for error message
                if ($this->waitForElement($this->getUiElement("/admin/messages/error"), 2)) {
                    $etext = $this->getText($this->getUiElement("/admin/messages/error"));
                    $this->setVerificationErrors("Check 1: Tax Creating error." . $etext);
                } else {
                    // Check for success message
                    if (!$this->waitForElement($this->getUiElement("/admin/messages/success"), 60)) {
                        $this->setVerificationErrors("Check 2: Tax Creating error. no success message");
                    } else {
                        $this->printInfo('Tax has been created');
                        }
                    }
            } else {
                $this->setVerificationErrors("Check 3: Tax Rule Creating error. No any messages from Magento. Hangs up ?");
                }
        $this->printDebug('Creating process finished');
    }
}