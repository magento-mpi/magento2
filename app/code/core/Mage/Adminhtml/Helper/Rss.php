<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Rss extends Mage_Core_Helper_Abstract
{
    /**
     * Authenticates admin user
     * Possible results:
     * - Mage_Admin_Model_User - user logged in and appropriate model is loaded
     * - true - user logged in, however no work can be done on it, because browser is redirected
     * - false - not used currently (it just exits when user is not logged in)
     *
     * @param $path
     * @return Mage_Admin_Model_User|true
     */
    public function authAdmin($path)
    {
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        if ($session->isAdminLoggedIn()) {
            return $session->getAdmin();
        }

        list($username, $password) = Mage::helper('Mage_Core_Helper_Http')->authValidate();
        $adminSession = Mage::getModel('Mage_Admin_Model_Session');
        $user = $adminSession->login($username, $password);
        if ($user === true) {
            return true;
        } else if ($user && $user->getIsActive() == '1' && $adminSession->isAllowed($path)){
            $session->setAdmin($user);
            return $user;
        } else {
            Mage::helper('Mage_Core_Helper_Http')->authFailed();
        }
    }
}
