<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminUser_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create Admin User.
     *
     * @param Array $userData
     */
    public function createAdminUser($userData)
    {
        $this->clickButton('add_new_admin_user');
        $this->fillForm($userData, 'user_info');
        $first = (isset($userData['first_name'])) ? $userData['first_name'] : '';
        $last = (isset($userData['last_name'])) ? $userData['last_name'] : '';
        $param = $first . ' ' . $last;
        $this->addParameter('elementTitle', $param);
        if (array_key_exists('role_name', $userData)) {
            $this->openTab('user_role');
            $this->searchAndChoose(array('role_name' => $userData['role_name']), 'permissions_user_roles');
        }
        $this->saveForm('save_admin_user');
    }

    /**
     * Login Admin User
     *
     * @param array $loginData
     *
     * @throws RuntimeException
     */
    public function loginAdmin($loginData)
    {
        $waitCondition = array($this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('pageelement', 'admin_logo'));
        $this->fillForm($loginData);
        $this->clickButton('login', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
        if ($this->controlIsPresent('link', 'go_to_notifications')) {
            try {
                $this->waitForElement($this->_getControlXpath('button', 'close'), 5)->click();
            } catch (RuntimeException $e) {
                if (strpos($e->getMessage(), 'Timeout after 5 seconds.') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Forgot Password Admin User
     *
     * @param array $emailData
     */
    public function forgotPassword($emailData)
    {
        $waitCondition = array($this->_getMessageXpath('general_success'), $this->_getMessageXpath('general_error'),
                               $this->_getMessageXpath('general_validation'));
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->checkCurrentPage('forgot_password'));
        $this->fillForm($emailData);
        $this->clickButton('retrieve_password', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
    }
}