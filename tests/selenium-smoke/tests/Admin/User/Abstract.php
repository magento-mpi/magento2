<?php
/**
 * Abstract test class for Admin/User module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_User_Abstract extends Test_Admin_Abstract
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
     * Performs login into the BackEnd
     *
     */
    public function addUser($name) {
      $this->debug("addUser started");
      // Open Manage Users Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/system/permissions/users"));
      // Add new user
      $this->clickAndWait($this->getUiElement("admin/pages/system/permissions/users/manageusers/buttons/addnewuser"));
      // Fill all fields
      $this->type($this->getUiElement("admin/pages/system/permissions/users/user/inputs/username"),$name);
      $this->type($this->getUiElement("admin/pages/system/permissions/users/user/inputs/firstname"),$name);
      $this->type($this->getUiElement("admin/pages/system/permissions/users/user/inputs/lastname"),$name);
      $this->type($this->getUiElement("admin/pages/system/permissions/users/user/inputs/email"),$name."@varien.com");
      $this->type($this->getUiElement("admin/pages/system/permissions/users/user/inputs/password"),"123123q");
      $this->type($this->getUiElement("admin/pages/system/permissions/users/user/inputs/confirmation"),"123123q");
      // Save user
      $this->clickAndWait($this->getUiElement("admin/pages/system/permissions/users/user/buttons/saveuser"));
      // Check for success message
      if (!$this->isElementPresent($this->getUiElement("admin/pages/system/permissions/users/user/messages/usersaved"))) {
        $this->setVerificationErrors("Check 1: no success message");
      }
      $this->debug("addUser finished");
    }

}

