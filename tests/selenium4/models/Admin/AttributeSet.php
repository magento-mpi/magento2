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
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->siteData = Core::getEnvConfig('backend/attribute_set');
    }

    /**
    * Adds new attribute set
    *
    * @param array $params May contain the following params:
    * set_name, default_set
    */
    public function doCreateAtrSet($params = array())
    {
        $result = true;
        $siteData = $params ? $params : $this->siteData;
        
        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manage_attibute_set"));
        // Add new Attribute Set
        $this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/manage_attribute_sets/buttons/add_set"));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/create_attribute_set');
        // Fill fields Name and Based On
        $this->type($this->getUiElement("input/set_name"),$siteData['set_name']);
        $this->select($this->getUiElement("input/based_on"),$siteData['default_set']);
        // Saving
        $this->clickAndWait($this->getUiElement("buttons/save_set"));
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/set_not_saved"))) {
            $etext = $this->getText($this->getUiElement("messages/set_not_saved"));
            $this->setVerificationErrors($etext);
            return false;
        } else {
        ($this->waitForElement($this->getUiElement("messages/set_saved"),20));
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
    public function doOpenAtrSet($params = array())
    {
        $result = true;
        $siteData = $params ? $params : $this->siteData;
        
	// Open Manage Attribute Sets Page
	$this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manage_attibute_set"));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets');
        // Search Attribute Set
	$this->type($this->getUiElement("input/search_by_name"),$siteData['set_name']);
	$this->clickAndWait($this->getUiElement("buttons/search"));
	if ($this->isElementPresent($this->getUiElement("locators/open_set",$siteData['set_name'])))  {
            $this->clickAndWait($this->getUiElement("locators/open_set",$siteData['set_name']));
	} else {
            $this->setVerificationErrors("Attribute Set does not exist");
            $result = false;
	}
        if ($result) {
            $this->printInfo('Attribute Set opened');
        }
        return $result;
    }

    /**
    * Delete attribute set
    *
    */
    public function doDeleteAtrSet() 
    {
        $result = true;
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manage_attribute_sets');
        // Delete Attribute Set
	$this->chooseOkOnNextConfirmation();
	$this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/edit_atrribute_set/buttons/delete_set"));
	$this->waitForElement($this->getUiElement("messages/set_deleted"),20);
	if (!$this->isElementPresent($this->getUiElement("messages/set_deleted"))) {
            $this->setVerificationErrors("no success message");
            $result = false;
	}
        if ($result) {
            $this->printInfo('Attribute Set deleted');
        }
        return $result;
    }
}