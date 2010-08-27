<?php
/**
 * Abstract test class for Admin/Attribute Set module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_AttributeSet_Abstract extends Test_Admin_Abstract
{
    /**
     * Helper local instance
     *
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
    public function addSet($setName)
    {
      Core::debug("addSet started");
      // Open Manage Attribute Sets
      $this->clickAndWait($this->getUiElement("admin/topmenu/catalog/attributes/manageAttibuteSet"));
      // Add new Attribute Set
      $this->clickAndWait($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSets/buttons/addSet"));
      // Fill fields Name and Based On
      $this->type($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSets/input/nameForSet"),$setName);
      $this->select($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSets/input/basedOn"),"label=Default");
      // Saving
      $this->click ($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSets/buttons/saveSet"));
      
      // Check for success message
      if (!$this->waitForElement($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSets/messages/setSaved"),10)) {
          $this->setVerificationErrors("addAttributeSet check 1: no success message");
          //Check for some specific validation errors:
          // name must be unique
          if ($this->isElementPresent($this->getUiElement("admin/pages/catalog/attributes/manageAttributeSets/messages/setNotSaved",$setName),2)) {
            $this->setVerificationErrors("addAttributeSet check 2: Attribute Name must be unique");
          }
      }

    }
}