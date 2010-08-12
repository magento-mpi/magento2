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
      $this->clickAndWait ($this->getUiElement("admin/topmenu/system/permissions/users"));
      $this->clickAndWait($this->getUiElement("admin/pages/system/permissions/users/manageusers/buttons/addnewuser"));
      $this->type($this->getUiElement("admin/pages/system/permissions/users/manageusers/inputs/username"),$name);
      $this->debug("addUser finished");
      sleep(10);
    }

}

