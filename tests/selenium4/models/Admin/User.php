<?php
/**
 * Admin_User_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_User extends Model_Admin {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->userData = Core::getEnvConfig('backend/user');
    }

    /**
     * Add user to the system from admin
     * @param name
     * @return boolean
     */
    public function doCreate($params = array())
    {
        $result = true;
        $userData = $params ? $params : $this->userData;
        $name = $this->userData['name'];
        $this->setUiNamespace('/admin/pages/system/permissions/users/user/');
        // Open Manage Users Page
        $this->clickAndWait(
            $this->getUiElement('/admin/topmenu/system/permissions/users')
        );
        // Add new user
        $this->clickAndWait($this->getUiElement('/admin/pages/system/permissions/users/manage_users/buttons/add_new_user'));
        // Fill all fields
        $this->type($this->getUiElement('inputs/user_name'),$name);
        $this->type($this->getUiElement('inputs/first_name'),$name);
        $this->type($this->getUiElement('inputs/last_name'),$name);
        $this->type($this->getUiElement('inputs/email'),$name.'@varien.com');
        $this->type($this->getUiElement('inputs/password'),'123123q');
        $this->type($this->getUiElement('inputs/confirmation'),'123123q');
        // Save user
        $this->clickAndWait($this->getUiElement('buttons/save'));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors('doCreate: ' . $etext);
            $result = false;
        } else {
        // Check for success message
          if (!$this->waitForElement($this->getUiElement('/admin/messages/success'),1)) {
            $this->setVerificationErrors('doCreate: no success message');
            $result = false;
          } else {
              $this->printInfo('User ' . $name . ' has been created');
              $result = true;
          }
        }
        return $result;
    }

    /**
     * Open user from admin
     * @param name
     * @return boolean
     */
    public function doOpen($params = array())
    {

        $userData = $params ? $params : $this->userData;
        $name = $this->userData['name'];
        $this->setUiNamespace('admin/pages/system/permissions/users/manage_users/');
        // Open Manage Users Page
        $this->clickAndWait(
            $this->getUiElement('/admin/topmenu/system/permissions/users')
        );
        // Filter users by name
        $this->click($this->getUiElement('buttons/reset_filter'));
        $this->pleaseWait();
        //Filter by username
        $this->type($this->getUiElement('filters/user_name'),$name);
        $this->click($this->getUiElement('buttons/search'));
        $this->pleaseWait();
        //Open user with 'User Name' == name
        //Determine Column with 'User Name' title
        $paramsArray = array (
           'User Name' => $name
        );

        $result = $this->getSpecificRow($this->getUiElement('elements/user_table'), $paramsArray);
        if ($result > -1 ) {
            $this->printDebug('User ' . $name . ' founded in ' . $result . ' row');
            $this->printDebug($this->getUiElement('elements/body') . '//tr['. $result .']');
            $this->clickAndWait($this->getUiElement('elements/body') . '//tr['. $result .']');
            $this->printDebug('User ' . $name . ' opened');
            return true;
        } else {
          $this->printDebug('doOpenUser finished with false');
          return false;
        }
    }

    /**
     * Delete user from admin
     * @param name
     * @return boolean
     */
    public function doDelete($params = array()) {
        $userData = $params ? $params : $this->userData;
        $name = $this->userData['name'];

        if ($this->doOpen($name)) {
            $this->clickAndWait($this->getUiElement('/admin/pages/system/permissions/users/user/buttons/delete'));
            $this->getConfirmation();
            // check for error message
            if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
                $etext = $this->getText($this->getUiElement('/admin/messages/error'));
                $this->setVerificationErrors('doDeleteUser: ' . $etext);
            } else {
              // Check for success message
              if ($this->waitForElement($this->getUiElement('/admin/messages/success'),1)) {
                $this->printInfo('User ' . $name . ' has been deleted');
              } else {
                $this->setVerificationErrors('doDeleteUser: no success message');
              }
            }
        }
    }
}
