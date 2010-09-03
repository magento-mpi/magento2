<?php
/**
 * Abstract test class for Admin/Attribute Set module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_AttributeSet_Abstract extends Test_Admin_Abstract {
    /**
    * Helper local instance
    * @var Helper_Admin
    */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();
    }

    /**
    * Adds new attribute set $SetName
    *@param $setName
    */
    public function doCreateAtrSet($setName) {
        Core::debug("addSet started");
        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement("admin/topmenu/catalog/attributes/manageAttibuteSet"));
        // Add new Attribute Set
        $this->clickAndWait($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/buttons/addSet"));
        // Fill fields Name and Based On
        $this->type($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/input/nameForSet"),$setName);
        $this->select($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/input/basedOn"),"label=Default");
        // Saving
        $this->clickAndWait($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/buttons/saveSet"));
        // check for error message
        if ($this->isElementPresent($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/messages/setNotSaved"),10)) {
            $etext = $this->getText($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/messages/setNotSaved"));
            $this->setVerificationErrors("Check 1: " . $etext);
            return false;
        } else {
            ($this->waitForElement($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/messages/setSaved"),20));
        }
        // Check for success message
        if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/attributes/createAttributeSetPage/messages/setSaved"),2)) {
            $this->setVerificationErrors("Check 2: no success message");
        }
        Core::debug("addSet finished");
    }
    public function doOpenAtrSet($setName) {
        // Open Manage Attribute Sets Page
        $this->clickAndWait($this->getUiElement("admin/topmenu/catalog/attributes/manageAttibuteSet"));
        // Search Attribute Set
        $this->type($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/input/searchByName"),$setName);
        $this->clickAndWait($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/buttons/search"));
        if ($this->isElementPresent($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/openSet",$setName),20))  {
            $this->clickAndWait($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/openSet",$setName));
        } else {
            $this->setVerificationErrors("Attribute Set does not exist");
        }
    }
    public function doDeleteAtrSet() {
        // Delete Attribute Set
        $this->chooseOkOnNextConfirmation();
        $this->clickAndWait($this->getUiElement("admin/pages/catalog/attributes/editAtrributeSetPage/buttons/deleteSet"));
        $this->waitForElement($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/messages/setDeleted"),20);
        if (!$this->isElementPresent($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSetsPage/messages/setDeleted"))) {
           $this->setVerificationErrors("no success message");
        }
    }
}