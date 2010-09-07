<?php
/**
 * Admin_Scope_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_AttributeSet_AddSet extends Model_Admin {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->siteData = Core::getEnvConfig('backend/attributeSet');
    }

    /**
    * Adds new attribute set
    *
    * @param array $params May contain the following params:
    * setName, defaultSet
    */
    public function doCreateAtrSet($params = array())
    {
        $siteData = $params ? $params : $this->siteData;
        Core::debug("addSet started");
        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manageAttibuteSet"));
        // Add new Attribute Set
        $this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/manageAttributeSetsPage/buttons/addSet"));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/createAttributeSetPage');
        // Fill fields Name and Based On
        $this->type($this->getUiElement("input/nameForSet"),$siteData['setName']);
        $this->select($this->getUiElement("input/basedOn"),"label=Default");
        // Saving
        $this->clickAndWait($this->getUiElement("buttons/saveSet"));
        // check for error message
        if ($this->isElementPresent($this->getUiElement("messages/setNotSaved"),10)) {
            $etext = $this->getText($this->getUiElement("messages/setNotSaved"));
            $this->setVerificationErrors("Check 1: " . $etext);
            return false;
        } else {
        ($this->waitForElement($this->getUiElement("messages/setSaved"),20));
        }
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("messages/setSaved"),2)) {
            $this->setVerificationErrors("Check 2: no success message");
        }
        Core::debug("addSet finished");
    }

    /**
    * Open attribute set
    *
    * @param array $params May contain the following params:
    * setName, defaultSet
    */
    public function doOpenAtrSet($params = array())
    {
        $siteData = $params ? $params : $this->siteData;
	// Open Manage Attribute Sets Page
	$this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/attributes/manageAttibuteSet"));
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manageAttributeSetsPage');
        // Search Attribute Set
	$this->type($this->getUiElement("input/searchByName"),$siteData['setName']);
	$this->clickAndWait($this->getUiElement("buttons/search"));
	if ($this->isElementPresent($this->getUiElement("openSet",$siteData['setName'])))  {
            $this->clickAndWait($this->getUiElement("openSet",$siteData['setName']));
	} else {
            $this->setVerificationErrors("Attribute Set does not exist");
	}
	}

    /**
    * Delete attribute set
    *
    */
    public function doDeleteAtrSet() {
        // Set UiNamespace
        $this->setUiNamespace('admin/pages/catalog/attributes/manageAttributeSetsPage');
        // Delete Attribute Set
	$this->chooseOkOnNextConfirmation();
	$this->clickAndWait($this->getUiElement("/admin/pages/catalog/attributes/editAtrributeSetPage/buttons/deleteSet"));
	$this->waitForElement($this->getUiElement("messages/setDeleted"),20);
	if (!$this->isElementPresent($this->getUiElement("messages/setDeleted"))) {
            $this->setVerificationErrors("no success message");
	}
	}
}