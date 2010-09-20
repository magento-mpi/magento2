<?php

/**
 * Admin_Scope_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_AttributeSet extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData() {
        parent::loadConfigData();

        $this->atrSetData = Core::getEnvConfig('backend/attribute_set');
    }

    /**
     * Adds new attribute set
     *
     * @param array $params May contain the following params:
     * set_name, default_set
     */
    public function doCreateAtrSet($params = array()) {
        $result = true;
        $atrSetData = $params ? $params : $this->atrSetData;

        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manage_attibute_set"));
        // Add new Attribute Set
        $this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/manage_attribute_sets/buttons/add_set"));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/create_attribute_set');
        // Fill fields Name and Based On
        $this->type($this->getUiElement("input/set_name"), $atrSetData['set_name']);
        $this->select($this->getUiElement("input/based_on"), $atrSetData['default_set']);
        // Saving
        $this->click($this->getUiElement("buttons/save_set"));
        $this->waitForElement($this->getUiElement("messages/set_saved"), 25);
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/set_not_saved"))) {
            $etext = $this->getText($this->getUiElement("messages/set_not_saved"));
            $this->setVerificationErrors($etext);
            return false;
        } else {
            ($this->waitForElement($this->getUiElement("messages/set_saved"), 40));
        }
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/set_saved"))) {
            $this->setVerificationErrors("Check 2: no success message");
            $result = false;
        }
        if ($result) {
            $this->printInfo('Attribute set created');
        }
        return $result;
    }

    /**
     * Open attribute set
     *
     * @param array $params May contain the following params:
     * set_name, default_set
     */
    public function doOpenAtrSet($params = array()) {
        $atrSetData = $params ? $params : $this->atrSetData;

        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manage_attibute_set"));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets');
        // Search Attribute Set
        $this->type($this->getUiElement("input/search_by_name"), $atrSetData['set_name']);
        $this->clickAndWait($this->getUiElement("buttons/search"));
        //Determining the number of Attribute Sets which contains word $atrSetData['set_name']
        $qtyRoot = $this->getXpathCount($this->getUiElement("locators/open_set", $atrSetData['set_name']));
        if ($qtyRoot == 1) {
            $nameRoot = $this->getText($this->getUiElement("locators/open_set", $atrSetData['set_name']));
            $nameRoot = trim($nameRoot);
            $this->printInfo($nameRoot);
            if ($atrSetData['set_name'] == $nameRoot) {
                $this->clickAndWait($this->getUiElement("locators/open_set", $atrSetData['set_name']));
                $this->printInfo("Attribute Set with name '" . $atrSetData['set_name'] . "' opened");
            } else {
                $this->printInfo("Attribute Set with name '" . $atrSetData['set_name'] . "' does not exist");
            }
        }
        if ($qtyRoot == 0) {
            $this->printInfo("Attribute Set with name '" . $atrSetData['set_name'] . "' does not exist");
        }
        $j = 0;
        if ($qtyRoot >= 2) {
            for ($i = 1; $i <= $qtyRoot; $i++) {
                $mas = array($atrSetData['set_name'], $i);
                $nameRoot = $this->getText($this->getUiElement("locators/set_many", $mas));
                $nameRoot = trim($nameRoot);
                if ($nameRoot == $atrSetData['set_name']) {
                    $j = $i;
                }
            }
            if ($j > 0) {
                $mas = array($atrSetData['set_name'], $j);
                $this->clickAndWait($this->getUiElement("locators/set_many", $mas));
                $this->printInfo("Attribute Set with name '" . $atrSetData['set_name'] . "' opened");
            } else {
                $this->printInfo("Attribute Set with name '" . $atrSetData['set_name'] . "' does not exist");
            }
        }
    }

    /**
     * Delete attribute set
     *
     */
    public function doDeleteAtrSet() {
        $result = true;
        $this->doOpenAtrSet();
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets');
        // Delete Attribute Set
        if ($this->isElementPresent($this->getUiElement("/admin/pages/catalog/attributes/edit_atrribute_set/buttons/delete_set"))) {
            $this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/edit_atrribute_set/buttons/delete_set"));
            $this->assertConfirmation("All products of this set will be deleted! Are you sure you want to delete this attribute set?");
            $this->chooseOkOnNextConfirmation();
            $this->waitForElement($this->getUiElement("messages/set_deleted"), 20);
            if (!$this->isElementPresent($this->getUiElement("messages/set_deleted"))) {
                $this->setVerificationErrors("no success message");
                $result = false;
            }
            if ($result) {
                $this->printInfo('Attribute Set deleted');
            }
        }
    }

}