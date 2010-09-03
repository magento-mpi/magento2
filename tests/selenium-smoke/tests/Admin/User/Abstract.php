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
     * Add user to the system from admin
     * @param name
     */
    public function addUser($name) {
      Core::debug('addUser started',7);
      // Open Manage Users Page
      $this->clickAndWait ($this->getUiElement("admin/topmenu/system/permissions/users"));
      // Add new user
      $this->clickAndWait($this->getUiElement("admin/pages/system/permissions/users/manageusers/buttons/addnewuser"));
      // Fill all fields
      $UIContext = 'admin/pages/system/permissions/users/user/';
      $this->type($this->getUiElement($UIContext . "inputs/username"),$name);
      $this->type($this->getUiElement($UIContext . "inputs/firstname"),$name);
      $this->type($this->getUiElement($UIContext . "inputs/lastname"),$name);
      $this->type($this->getUiElement($UIContext . "inputs/email"),$name."@varien.com");
      $this->type($this->getUiElement($UIContext . "inputs/password"),"123123q");
      $this->type($this->getUiElement($UIContext . "inputs/confirmation"),"123123q");
      // Save user
      $this->clickAndWait($this->getUiElement($UIContext . "buttons/saveUser"));
      // check for error message
      if ($this->waitForElement($this->getUiElement("admin/messages/error"),1)) {
        $etext = $this->getText($this->getUiElement("admin/messages/error"));
        $this->setVerificationErrors("Check 1: " . $etext);
      } else {
      // Check for success message
          if (!$this->waitForElement($this->getUiElement("admin/messages/success"),1)) {
            $this->setVerificationErrors("Check 2: no success message");
          }
      }
      Core::debug('addUser finished',7);
    }

    /**
     * Open user from admin
     * @param name
     * @return boolean
     */
    public function doOpenUser($name) {
      Core::debug('doOpenUser started',7);
      // Open Manage Users Page
      $this->clickAndWait($this->getUiElement('admin/topmenu/system/permissions/users'));
      $UIContext = 'admin/pages/system/permissions/users/manageusers/';
      // Filter users by name
      $this->click($this->getUiElement($UIContext . 'buttons/resetFilter'));
      $this->pleaseWait();
      $this->type($this->getUiElement($UIContext . 'filters/username'),$name);
      $this->click($this->getUiElement($UIContext . 'buttons/search'));
      $this->pleaseWait();

      //Open user with 'User Name' == name
      //Determine Column with 'User Name' title
      $colNum = $this->getXpathCount($this->getUiElement($UIContext . 'elements/userTable') . "//tr[contains(@class,'heading')]//th");
      $headingLocator =  $this->getUiElement($UIContext . 'elements/userTable');
      $userNameColInd = -1;
      for ($x = 0; $x<= $colNum-1; $x++) {
        $cellLocator = $headingLocator . '.0.' . $x ;
        $cell = $this->getTable($cellLocator);
        if ('User Name' == $cell) {
            $userNameColInd = $x;
        }
      }
      //Find cell in UserName column with matched cell.text() == $name
      $result = -1;
      if ($userNameColInd >-1) {
          $rowNum = $this->getXpathCount($this->getUiElement($UIContext . 'elements/userTable') . "//tbody//tr");
          $bodyLocator =  $this->getUiElement($UIContext . 'elements/userTable') . '//tbody';
          for ($row = 0; $row <= $rowNum-1; $row++) {
            $cellLocator = $bodyLocator . '.' . $row . '.' . $userNameColInd  ;
            $cell = $this->getTable($cellLocator);
            if ($name == $cell) {
                $result = $row;
                Core::debug('Founded in ' . $cellLocator );
            }
          }
      }
      if ($result > -1 ) {
        // user has been founded
        $result++;
        $this->clickAndWait($bodyLocator . '//tr['. $result .']');
        Core::debug('doOpenUser finished with true',7);
        return true;
      } else {
          Core::debug('doOpenUser finished with false',7);
          return false;
      }

    }

    public function doDeleteUser($userName) {
        Core::debug('doDeleteUser started',7);
        if ($this->doOpenUser($userName)) {
            $this->clickAndWait($this->getUiElement('admin/pages/system/permissions/users/user/buttons/deleteUser'));
            $this->getConfirmation();
            // check for error message
            if ($this->waitForElement($this->getUiElement("admin/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("admin/messages/error"));
            $this->setVerificationErrors("doDeleteUser: " . $etext);
            } else {
            // Check for success message
              if (!$this->waitForElement($this->getUiElement("admin/messages/success"),1)) {
                $this->setVerificationErrors("doDeleteUser: no success message");
                Core::debug('User ' . $userName . ' has been deleted',5);
              }
            }
        }
        Core::debug('doDeleteUser finished with false',7);
    }
}

